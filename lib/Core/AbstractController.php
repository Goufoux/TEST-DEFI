<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Module\Notifications;
use Service\Request;
use Service\Response;
use Module\Logger;
use Module\Menu;

abstract class AbstractController
{
    protected $app;
    protected $twig;
    protected $manager;
    protected $notifications;
    protected $path;
    protected $base_link;
    protected $request;
    protected $response;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->loadTwig();
        $this->notifications = Notifications::getInstance();
        $this->manager = new Managers($app->getDatabase()->bdd());
        $this->request = new Request;
        $this->response = new Response;
    }

    public function setBaseLink(Route $route)
    {
        $request = $route->getElements();
        // if (!$route->getIndexAccess()) {
        //     array_pop($request);
        // }
        $temp = implode('/', $request);
        $this->base_link = '/'.$temp.'/';

        return $this;
    }

    public function render(array $array = [], string $path = null)
    {
        $this->setPath($path);
        $data = array();
        foreach ($array as $key => $val) {
            $data[$key] = $val;
        }
        $data['app'] = $this->app;
        $data['request'] = $this->request;
        $data['base_url'] = $this->base_link;

        if ($this->app->routeur()->getRoute()->getInterface() !== 'backend') {
            $menu = new Menu($this->app);

            $menu = $menu->launch();
            $data['menu'] = $menu;
            $flag = [
                'ORDER BY' => [
                    'table' => 'rubrique',
                    'tag' => 'name',
                    'type' => 'ASC' 
                ]
            ];
            $rubriques = $this->manager->fetchAll('rubrique', $flag);
            $data['rubriques'] = $rubriques;
        } else {
            $data['interface'] = 'Backend';
            if (isset($this->app->routeur()->getMatch()['module'])) {
                $data['module'] = $this->app->routeur()->getMatch()['module'];
                $elements = $this->app->routeur()->getRoute()->getElements();
                if ('index' !== $this->app->routeur()->getView()) {
                    array_pop($elements);
                }
                $url = implode('/', $elements);
                $data['module_url'] = '/'.$url;
            }
            if (isset($this->app->routeur()->getMatch()['view'])) {
                $data['view'] = $this->app->routeur()->getMatch()['view'];
            }
        }

        if (!empty($_SESSION['datas'])) {
            $data['datas'] = $_SESSION['datas'];
            unset($_SESSION['datas']);
        }

        if (!empty($_SESSION['form'])) {
            $data['form'] = $_SESSION['form'];
            unset($_SESSION['form']);
        }

        return $this->twig->render($this->getPath(), $data);
    }

    public function loadTwig()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../template/');
        $this->twig = new \Twig_Environment($loader, [
            'cache' => 'cache/twig-cache',
            'debug' => true
        ]);
        $this->twig->addExtension(new \Twig_Extension_Debug());
    }

    public function getPath()
    {
        return $this->path;
    }

    private function setPath($path = null)
    {
        $routePropriety = $this->app->routeur()->getMatch();
        
        $tempPath = explode('_', $routePropriety['name']);

        $interface = $this->app->routeur()->getRoute()->getInterface();

        $path = $interface.'/'.$tempPath[1].'/'.$tempPath[2].'.html.twig';

        if(false === file_exists(__DIR__.'/../../template/'.$path)) {
            // die('template not found : ' . $path);
        }
        
        $this->path = $path;

        return true;
    }

    public function isDev()
    {
        return ($this->app->config()->isDev() === true);
    }

    public function getAll($data = '', $method)
    {
        return $this->get($data, $method, true);
    }

    public function get($data = '', $method = 'GET', $all = false, $autoRedirect = true)
    {
        $result = $all ? $this->request->getAllData() : $this->request->getData($data);

        if ($method == 'POST') {
            $result = $all ? $this->request->getAllPost() : $this->request->getPost($data);
        }
        
        if ($autoRedirect && ($result === false || $result === null || empty($result))) {
            $this->notifications->default('500', 'Erreur', "$data n'a pas été trouvé. Méthode $method", 'danger', $this->isDev());

            $this->response->referer();
        }

        return $result;
    }
}
