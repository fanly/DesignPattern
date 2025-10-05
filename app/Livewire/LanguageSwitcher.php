<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Component
{
    public $currentLocale;
    public $availableLocales = ['zh' => '中文', 'en' => 'English'];

    public function mount()
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLanguage($locale)
    {
        if (array_key_exists($locale, $this->availableLocales)) {
            Session::put('locale', $locale);
            App::setLocale($locale);
            $this->currentLocale = $locale;
            
            // 重载页面以应用新的语言设置
            $this->redirect(request()->header('referer') ?? '/');
        }
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}