<?php


namespace mf\router;


use mf\auth\Authentification;

class Router extends AbstractRouter
{
    /**
     * Router constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        //créer une instance de la classe par défaut
        // et éxecute la méthode par défault de cette classe
        $auth = new Authentification();
        $route=self::$aliases['default'];
        $controllerName = self::$routes[$route][0];
        $methodName = self::$routes[$route][1];
        // Si un chemin valid a été demandé, créer instance et méthode concerné
        // à la place de celle par défaut
        if(isset(self::$routes[$this->http_req->path_info][2]))
            $accessReq =self::$routes[$this->http_req->path_info][2];
        else
            $accessReq = -999;
        if(isset(self::$routes[$this->http_req->path_info]) && $auth->checkAccessRight($accessReq)){
            $controllerName = self::$routes[$this->http_req->path_info][0];
            $methodName = self::$routes[$this->http_req->path_info][1];
        }
        $controller = new $controllerName();
        $controller->$methodName();
    }

    public function urlFor($route_name, $param_list = [])
    {
        $url = $this->http_req->script_name.$route_name;
        //$url = self::$routes[$route_name];
        if(count($param_list)>0){
            foreach($param_list as $paramName => $value){
                $url .= "?".$paramName."=".$value;
            }
        }
        return $url;
    }

    public function setDefaultRoute($url)
    {
        self::$aliases['default']=$url;
    }

    public function addRoute($name, $url, $controller, $method, $accessLevel = Authentification::ACCESS_LEVEL_NONE)
    {
        self::$routes[$url] = [$controller, $method, $accessLevel] ;
        self::$aliases[$name]=$url;

    }

    public static function executeRoute($alias){
        $route=self::$aliases[$alias];
        $controlName = self::$routes[$route][0];
        $methodName = self::$routes[$route][1];

        $controller = new $controlName();
        $controller->$methodName();
    }
}