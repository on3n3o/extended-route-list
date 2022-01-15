<?php

namespace On3n3o\ExtendedRouteList\Console;

class ExtendedRouteListCommand extends \Illuminate\Console\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extended-route-list:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a list of all routes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Generating route list...');

        $routes = \Route::getRoutes();

        $routeList = [];

        foreach ($routes as $route) {
            $routeList[] = [
                'method' => $route->getMethods(),
                'uri' => $route->getUri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => $route->gatherMiddleware(),
            ];
        }

        $this->info('Route list generated.');

        $this->info('Writing route list to file...');

        $file = fopen(config('extended-route-list.file'), 'w');

        fwrite($file, json_encode($routeList, JSON_PRETTY_PRINT));

        fclose($file);

        $this->info('Route list written to file.');
    }
} 