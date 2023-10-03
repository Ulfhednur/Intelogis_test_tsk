<?php
namespace Lib;

use JetBrains\PhpStorm\ArrayShape;

class Input extends Filter
{
    public Filter $headers;
    public Filter $request;
    public ?Filter $body;

    public function __construct()
    {
        $this->headers = new Filter(getallheaders());
        $request = $_GET;
        $route = self::parseRoute();
        $request['controller'] = $route['controller'];
        $request['task'] = $route['task'];
        $this->request = new Filter($request);

        $contentType = explode(';', $this->headers->getString('Content-Type'))[0];
        $this->body = match ($contentType){
            'text/json' => new Filter(json_decode(file_get_contents('php://input'), true)),
            'multipart/form-data',
            'application/x-www-form-urlencoded', => new Filter($_POST),
            default => new Filter([]),
        };

        $items = [];
        foreach($this->request->asArray() as $k => $v) {
            $items[$k] = $v;
        }
        foreach($this->body->asArray() as $k => $v) {
            $items[$k] = $v;
        }
        parent::__construct($items);
    }

    protected static function parseRoute(): array
    {
        $path = explode('?', $_SERVER['REQUEST_URI'])[0];
        $path = explode('/', $path);
        foreach($path as $key => $value){
            if(empty($value)) {
                unset($path[$key]);
            }
        }
        $path = array_values($path);
        return [
            'controller' => $path[0] ?? 'calculator',
            'task' => $path[1] ?? 'index',
        ];
    }
}