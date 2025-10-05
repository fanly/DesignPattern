<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Route;

class SidebarAdminLinks extends Component
{
    public $currentRoute;

    public function mount()
    {
        $this->currentRoute = Route::currentRouteName();
    }

    public function render()
    {
        return view('livewire.auth.sidebar-admin-links');
    }
}
