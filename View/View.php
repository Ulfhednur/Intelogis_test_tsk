<?php

namespace View;

use Helpers\RendererHelper;
use Lib\Application;

class View
{
    public function display()
    {
        $app = Application::getInstance();
        return RendererHelper::render('Layouts/index', ['app' => $app]);
    }
}