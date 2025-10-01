<?php

namespace App\Livewire;

use Livewire\Component;
use Usamamuneerchaudhary\Commentify\Models\Comment;
use Illuminate\Database\Eloquent\Model;

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
            ->with('user')
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
}