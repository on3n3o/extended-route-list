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
        
        $this->middlewareFormat = config('extended-route-list.middleware.format');
        $this->middlewareLinestyle = config('extended-route-list.middleware.linestyle');
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

        return $this->filterRoute([
            'domain' => $route->domain(),
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => Str::afterLast(ltrim($route->getActionName(), '\\'), '\\'),
            'middleware' => $this->getMiddleware($route),
            'package' => $this->findInDocBlocks('@package', $docBlock, $functionDocBlock),
            'author' => $this->findInDocBlocks('@author', $docBlock, $functionDocBlock),
            'version' => $this->findInDocBlocks('@version', $docBlock, $functionDocBlock),
            'since' => $this->findInDocBlocks('@since', $docBlock, $functionDocBlock),
            'access' => $this->findInDocBlocks('@access', $docBlock, $functionDocBlock),
            'link' => $this->findInDocBlocks('@link', $docBlock, $functionDocBlock),
            'see' => $this->findInDocBlocks('@see', $docBlock, $functionDocBlock),
            'example' => $this->findInDocBlocks('@example', $docBlock, $functionDocBlock),
            'todo' => $this->findInDocBlocks(['@todo', '@fixme'], $docBlock, $functionDocBlock),
            /** The deprecated should return true or false */
            'deprecated' => $this->findInDocBlocks('@deprecated', $docBlock, $functionDocBlock),
            'uses' => $this->findInDocBlocks('@uses', $docBlock, $functionDocBlock),
            'param' => $this->findInDocBlocks('@param', $docBlock, $functionDocBlock),
            'return' => $this->findInDocBlocks('@return', $docBlock, $functionDocBlock),
            'throws' => $this->findInDocBlocks('@throws', $docBlock, $functionDocBlock),
            // This inheritdoc shoud be implemented as logic in the future
            // and not as a string in the docblock
            // when this tag is present in the docblock it should be
            // inherited from the parent docblock
            '@inheritdoc' => $this->findInDocBlocks('@inheritdoc', $docBlock, $functionDocBlock),
            'license' => $this->findInDocBlocks('@license', $docBlock, $functionDocBlock),
        ]);
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
        return implode(PHP_EOL, $finds);
    }
}
