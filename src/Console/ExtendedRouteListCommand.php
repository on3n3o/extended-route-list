<?php

namespace On3n3o\ExtendedRouteList\Console;

use Illuminate\Foundation\Console\RouteListCommand as IlluminateRouteListCommand;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;

class ExtendedRouteListCommand extends IlluminateRouteListCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'route:list';

    /**
     * The table headers for the command.
     *
     * @var string[]
     */
    protected $headers = ['Domain', 'Method', 'URI', 'Name', 'Action', 'Middleware', 'Docs'];

    /**
     * The columns to display when using the "compact" flag.
     *
     * @var string[]
     */
    protected $compactColumns = ['method', 'uri', 'action', 'docs'];
    /**
     * Create a new route command instance.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        parent::__construct($this->router);
    }

    /**
     * Get the route information for a given route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return array
     */
    protected function getRouteInformation(Route $route)
    {
        return $this->filterRoute([
            'domain' => $route->domain(),
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => Str::afterLast(ltrim($route->getActionName(), '\\'), '\\'),
            'middleware' => $this->getMiddleware($route),
            'docs' => $this->getDocs($route),
        ]);
    }

    protected function getMiddleware($route)
    {
        $middleware = parent::getMiddleware($route);
        $middleware = explode(PHP_EOL, $middleware);
        $middleware = array_map(function ($item) {
            return Str::afterLast($item, '\\');
        }, $middleware);
        return implode(' ', $middleware) ?: '-';
    }

    /**
     * Get the docs for given route.
     * @param  \Illuminate\Routing\Route  $route
     * @return string
     */
    protected function getDocs(Route $route)
    {
        $className = Str::before($route->getActionName(), '@');
        $fileName = Str::replace('\\', '/', lcfirst($className));
        try {
            $file = file_get_contents(base_path() . '/' . $fileName . '.php');
        } catch (\Exception $e) {
            return null;
        }

        if (Str::contains($file, '@see')) {
            $doc = Str::words(Str::after($file, '@see '), 1, '');
            return $doc;
        }
        return null;
    }
}
