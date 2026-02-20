<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    public ?string $title;

    public function __construct(?string $title = null)
    {
        $this->title = $title;
    }

    public function render()
    {
        return view('components.app-layout');
    }
}
