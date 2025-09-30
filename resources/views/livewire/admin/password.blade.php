<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * 更新管理员密码
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('password-updated');
    }
}; ?>

<div style="max-width: 500px; margin: 0 auto; padding: 20px;">
    <div style="margin-bottom: 30px;">
        <h2 style="font-size: 24px; font-weight: 600; color: #1f2937; margin-bottom: 8px;">修改密码</h2>
        <p style="color: #6b7280; font-size: 14px;">请确保使用一个长且随机的密码来保证账户安全</p>
    </div>

    <form wire:submit="updatePassword" style="background: white; padding: 24px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">当前密码</label>
            <input 
                type="password" 
                wire:model="current_password" 
                required
                autocomplete="current-password"
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"
                placeholder="请输入当前密码"
            >
            @error('current_password')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">新密码</label>
            <input 
                type="password" 
                wire:model="password" 
                required
                autocomplete="new-password"
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"
                placeholder="请输入新密码"
            >
            @error('password')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">确认新密码</label>
            <input 
                type="password" 
                wire:model="password_confirmation" 
                required
                autocomplete="new-password"
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"
                placeholder="请再次输入新密码"
            >
            @error('password_confirmation')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; align-items: center; gap: 16px;">
            <button 
                type="submit" 
                style="background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer;"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>保存更改</span>
                <span wire:loading>保存中...</span>
            </button>

            <div style="color: #10b981; font-size: 14px;" wire:model="password-updated">
                <span style="display: none;" wire:loading.class.remove="hidden">密码已更新！</span>
            </div>
        </div>
    </form>
</div>