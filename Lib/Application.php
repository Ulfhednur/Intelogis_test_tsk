<?php
namespace Lib;

use Helpers\RendererHelper;
use Lib\Input;
use View\View;

class Application
{
    public const RENDER_TYPE_HTML = 'html';
    public const RENDER_TYPE_BODY = 'body';

    public static self $instance;

    protected Input $input;
    protected array $headers = [];
    protected string $renderType = self::RENDER_TYPE_HTML;
    protected int $httpCode = 200;
    protected string $httpError;
    public string $title;
    public string $body;

    public function __construct()
    {
        self::$instance = $this;
    }

   public function run()
    {
        if(!$this->renderComponent() || $this->httpCode != 200) {
            $this->renderError();
        }
        $this->sendHeaders();

        echo match ($this->renderType) {
            self::RENDER_TYPE_HTML => (new View())->display(),
            self::RENDER_TYPE_BODY => $this->body
        };

        exit(0);
    }

    public function setHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
    }

    public function setHTTPCode(int $code)
    {
        $this->httpCode = $code;
    }

    public function setError(string $message)
    {
        $this->httpError = $message;
    }

    public function setRenderType(string $type)
    {
        $this->renderType = $type;
    }

    public static function getInstance(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getInput(): Input
    {
        if(empty($this->input)) {
            $this->input = new Input();
        }
        return $this->input;
    }

    protected function sendHeaders()
    {
        foreach($this->headers as $header => $value) {
            header(sprintf('%s: %s', $header, $value));
        }
    }

    protected function renderComponent(): bool
    {
        $input = $this->getInput();

        $controllerName = '\\Controller\\'.ucfirst(strtolower($input->getCmd('controller', 'calculator'))) . 'Controller';
        if(!class_exists($controllerName)) {
            $this->setHTTPCode(404);
            $this->setError('Not Found');
            return false;
        }

        $task = $input->getCmd('task', 'Index');
        if(!method_exists($controllerName, $task)) {
            $this->setHTTPCode(404);
            $this->setError('Not Found');
            return false;
        }

        $controller = new $controllerName();
        if ($this->body = $controller->$task()) {
            return true;
        }
        return false;
    }

    protected function renderError()
    {
        $error = [
            'code' => $this->httpCode,
            'message' => $this->httpError
        ];
        $this->title = 'Error ' . $this->httpCode;
        match ($this->renderType) {
            self::RENDER_TYPE_HTML => RendererHelper::render('Errors/index', ['error' => $error]),
            self::RENDER_TYPE_BODY => json_encode($error)
        };
    }
}