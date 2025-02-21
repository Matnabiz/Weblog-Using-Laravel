<?php

class Router
{
    private $routes = [];

    public function get($path, $callback){
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback){
        $this->routes['POST'][$path] = $callback;
    }   

    public function resolve(){
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $callback = $this->routes[$method][$path] ?? false;
        if (!$callback) {
            echo "404 - Page Not Found";
            return;
        }

        call_user_func($callback);
    }
}
