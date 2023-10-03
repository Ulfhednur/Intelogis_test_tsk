<?php
namespace Controller;

use Lib\Input;
use Lib\Application;
use Helpers\RendererHelper;

abstract class AbstractController
{
    private Input $input;
    protected Application $app;

    public function __construct()
    {
        $this->app = Application::getInstance();
    }

    protected function render(string $path, array $params = []): string
    {
        $path = str_replace(['Controller', '\\'], '', get_class($this)) . DIRECTORY_SEPARATOR . $path;
        $this->app->setHeader('Content-Type', 'text/html');
        $this->app->setRenderType(Application::RENDER_TYPE_HTML);
        return RendererHelper::render($path, $params);
    }

    protected function asJson(mixed $data): string
    {
        $this->app->setHeader('Content-Type', 'text/json');
        $this->app->setRenderType(Application::RENDER_TYPE_BODY);
        return json_encode($data);
    }

    protected function getInput(): Input
    {
        if(empty($this->input)) {
            $this->input = $this->app->getInput();
        }
        return $this->input;
    }

    public abstract function index(): string;
}