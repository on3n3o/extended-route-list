<?php

namespace On3n3o\ExtendedRouteList\Console;

use Illuminate\Foundation\Console\RouteListCommand as IlluminateRouteListCommand;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

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
    protected $headers;

    /**
     * The columns to display when using the "compact" flag.
     *
     * @var string[]
     */
    protected $compactColumns;

    /**
     * The columns to display when using no flag.
     *
     * @var string[]
     */
    protected $normalColumns;

    /**
     * The columns to display when using "all-columns" flag.
     *
     * @var string[]
     */
    protected $allColumns;

    /**
     * The casts between column name and tags.
     *
     * @var string[]
     */
    protected $casts;

    /**
     * The setting how to format the name of the middleware.
     * 
     * @var string
     */
    protected $middlewareFormat;

    /**
     * The middleware setting how to format line endings.
     * 
     * @var string
     */
    protected $middlewareLinestyle;

    /**
     * The Json Option for formatting --json output.
     * 
     * @see https://www.php.net/manual/en/json.constants.php
     * 
     * @var int
     */
    protected $jsonOptions;

    /**
     * The setting to drop column if empty in json output.
     * 
     * @var string
     */
    protected $jsonDropIfEmpty;

    /**
     * Create a new route command instance.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->headers = config('extended-route-list.columns');
        $this->compactColumns = array_map('strtolower', config('extended-route-list.compact_columns'));
        $this->normalColumns = array_map('strtolower', config('extended-route-list.normal_columns'));
        $this->allColumns = array_map('strtolower', config('extended-route-list.all_columns'));
        $this->casts = config('extended-route-list.casts');
        
        $this->middlewareFormat = config('extended-route-list.config.middleware.format');
        $this->middlewareLinestyle = config('extended-route-list.config.middleware.linestyle');
        $this->actionFormat = config('extended-route-list.config.action.format');

        $this->jsonOptions = config('extended-route-list.config.json.options');
        $this->jsonDropIfEmpty = config('extended-route-list.config.json.drop_column_if_empty');

        parent::__construct($this->router);
    }

    /**
     * Get the column names to show (lowercase table headers).
     *
     * @return array
     */
    protected function getColumns()
    {
        $availableColumns = array_map('strtolower', $this->headers);

        if ($this->option('compact')) {
            return array_intersect($availableColumns, $this->compactColumns);
        }

        if ($columns = $this->option('columns')) {
            return array_intersect($availableColumns, $this->parseColumns($columns));
        }

        if ($this->option('all-columns')) {
            return array_intersect($availableColumns, $this->allColumns);
        }

        return array_intersect($availableColumns, $this->normalColumns);

        return $availableColumns;
    }

    /**
     * Get the route information for a given route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return array
     */
    protected function getRouteInformation(Route $route)
    {
        $file = $this->getClassFile($route);
        $docBlock = $this->getDocBlock($file);
        $functionName = Str::after($route->getActionName(), '@');
        $functionDocBlock = $this->getFunctionDocBlock($file, $functionName);
        $finds = [];

        foreach($this->casts as $cast){
            $finds[$cast['column_name']] = $this->findInDocBlocks($cast['tags'], $docBlock, $functionDocBlock);
        }

        return $this->filterRoute(array_merge([
            'domain' => $route->domain(),
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $this->actionFormat == 'full' ? $route->getActionName() : Str::afterLast(ltrim($route->getActionName(), '\\'), '\\'),
            'middleware' => $this->getMiddleware($route),
        ], $finds));
    }

    protected function getMiddleware($route)
    {
        $middleware = parent::getMiddleware($route);
        $middleware = explode(PHP_EOL, $middleware);
        if ($this->middlewareFormat == 'short') {
            $middleware = array_map(function ($item) {
                return Str::afterLast($item, '\\');
            }, $middleware);
        }

        return implode($this->middlewareLinestyle == 'multi' ? PHP_EOL : ' ', $middleware) ?: '-';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['all-columns', 'a', InputOption::VALUE_NONE, 'Show all columns'],
        ]);
    }

    protected function getClassFile(Route $route)
    {
        $className = Str::before($route->getActionName(), '@');
        $fileName = Str::replace('\\', '/', lcfirst($className));
        try {
            return file_get_contents(base_path() . '/' . $fileName . '.php');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getDocBlock($file)
    {
        $lines = explode(PHP_EOL, $file);
        $doc = [];
        $start = false;
        foreach ($lines as $line) {
            if (Str::contains($line, '/*')) {
                $start = true;
            }
            if ($start) {
                $doc[] = $line;
            }
            if (Str::contains($line, '*/')) {
                break;
            }
            // Search only for the first docblock before the class definition
            if (Str::contains($line, 'class')) {
                break;
            }
        }
        return $doc;
    }

    protected function getFunctionDocBlock($file, $functionName)
    {
        $lines = explode(PHP_EOL, $file);
        $doc = [];
        $start = false;
        $lines = array_reverse($lines);
        
        foreach ($lines as $line) {
            if (Str::contains($line, 'function ' . $functionName . '(')) {
                // info('Found function ' . $functionName);
                $start = true;
            }
            if ($start) {
                $doc[] = $line;
            }
            if (Str::contains($line, '/**') && $start) {
                break;
            }
        }
        $doc = array_reverse($doc);
        return $doc;
    }

    protected function findInDocBlock($docBlockLines, $tags)
    {
        $finds = [];
        foreach ($docBlockLines as $line) {
            if (Str::contains($line, $tags)) {
                if(is_array($tags)){
                    foreach ($tags as $tag) {
                        if (Str::contains($line, $tag)) {
                            $finds[] = trim(Str::after($line, $tag));
                        }
                    }
                }else{
                    $finds[] = trim(Str::after($line, $tags));
                }
            }
        }

        return $finds;
    }

    protected function findInDocBlocks($tags, ...$docBlocks)
    {
        $finds = [];
        foreach ($docBlocks as $docBlock) {
            $finds = array_merge($finds, $this->findInDocBlock($docBlock, $tags));
        }
        if(empty($finds)){
            return null;
        }
        return implode(PHP_EOL, $finds);
    }

    /**
     * Convert the given routes to JSON.
     *
     * @param  array  $routes
     * @return string
     */
    protected function asJson(array $routes)
    {
        return collect($routes)
            ->map(function ($route) {
                
                $route['middleware'] = empty($route['middleware']) ? [] : explode("\n", $route['middleware']);
                foreach($this->casts as $cast){
                    if($cast['linestyle'] == 'multi'){
                        $route[$cast['column_name']] = empty($route[$cast['column_name']]) ? [] : explode("\n", $route[$cast['column_name']]);
                    }
                }

                if($this->jsonDropIfEmpty){
                    $route = array_filter($route);
                }
                return $route;
            })
            ->values()
            ->toJson($this->jsonOptions);
    }
}
