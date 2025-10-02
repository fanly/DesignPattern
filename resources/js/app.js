import './bootstrap';

// Livewire组件更新后的处理
document.addEventListener('DOMContentLoaded', function() {
    // 监听Livewire组件更新
    if (window.Livewire) {
        Livewire.hook('message.processed', (message, component) => {
            // 可以在这里添加组件更新后的处理逻辑
            console.log('Livewire组件已更新:', component.name);
        });
    }
});