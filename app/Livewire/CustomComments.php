<?php

namespace App\Livewire;

use Livewire\Component;
use Usamamuneerchaudhary\Commentify\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomComments extends Component
{
    public Model $model;
    
    public $newCommentState = [
        'body' => ''
    ];

    public function mount(Model $model)
    {
        $this->model = $model;
    }

    public function render()
    {
        $comments = Comment::where('commentable_type', get_class($this->model))
            ->where('commentable_id', $this->model->id)
            ->with(['user', 'likes'])
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.custom-comments', [
            'comments' => $comments
        ]);
    }
    
    public function postComment()
    {
        $this->validate([
            'newCommentState.body' => 'required'
        ]);

        // 检查用户是否登录
        if (!auth()->check()) {
            session()->flash('error', '请先登录后再评论！');
            return;
        }

        try {
            // 使用更安全的方式创建评论，避免fillable属性问题
            $comment = new Comment();
            $comment->body = $this->newCommentState['body'];
            $comment->commentable_type = get_class($this->model);
            $comment->commentable_id = $this->model->id;
            $comment->user_id = auth()->id();
            $comment->save();

            $this->newCommentState = [
                'body' => ''
            ];
            
            session()->flash('message', '评论发布成功！');
        } catch (\Exception $e) {
            session()->flash('error', '评论发布失败：' . $e->getMessage());
        }
    }

    public function toggleLike($commentId)
    {
        if (!auth()->check()) {
            session()->flash('error', trans('commentify::commentify.login_to_like'));
            return;
        }

        try {
            $comment = Comment::findOrFail($commentId);
            
            // 检查用户是否已经点赞
            $isLiked = $this->isLikedByUser($commentId);
            
            if ($isLiked) {
                // 取消点赞
                $comment->likes()->where('user_id', auth()->id())->delete();
                session()->flash('message', trans('commentify::commentify.unlike_success'));
            } else {
                // 创建点赞
                $comment->likes()->create([
                    'user_id' => auth()->id(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
                session()->flash('message', trans('commentify::commentify.like_success'));
            }
        } catch (\Exception $e) {
            session()->flash('error', '操作失败：' . $e->getMessage());
        }
    }

    public function isLikedByUser($commentId)
    {
        if (!auth()->check()) {
            return false;
        }

        $comment = Comment::find($commentId);
        if (!$comment) {
            return false;
        }
        
        // 检查当前用户是否点赞过这条评论
        return $comment->likes()->where('user_id', auth()->id())->exists();
    }
}