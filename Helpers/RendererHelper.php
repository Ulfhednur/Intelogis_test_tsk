<?php

namespace Helpers;

abstract class RendererHelper
{
    protected const TMPL_DIR = 'View';

    public static function getTmplPath(): string
    {
        return APP_PATH . DIRECTORY_SEPARATOR . self::TMPL_DIR;
    }

    public static function render(string $path, array $params = []): string
    {
        ob_start();
        ob_implicit_flush(false);
        if(!empty($params)) {
            foreach ($params as $key => $value) {
                $$key = $value;
            }
        }
        require self::getTmplPath() . DIRECTORY_SEPARATOR . $path . '.php';
        return ob_get_clean();
    }
}