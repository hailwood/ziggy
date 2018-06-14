<?php

namespace Tightenco\Ziggy;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Tightenco\Ziggy\BladeRouteGenerator;
use Tightenco\Ziggy\RoutePayload;

class CommandTypescriptGenerator extends Command
{
    protected $signature = 'ziggy:typescript {path=./resources/assets/js/types/ziggy.d.ts}';

    protected $description = 'Generate a TypeScript definition file';

    protected $baseUrl;
    protected $baseProtocol;
    protected $baseDomain;
    protected $basePort;
    protected $files;
    protected $router;

    public function __construct(Router $router, Filesystem $files)
    {
        parent::__construct();

        $this->router = $router;
        $this->files = $files;
    }

    public function handle()
    {
        $path = $this->argument('path');

        $generatedRoutes = $this->generate();

        $this->makeDirectory($path);

        $this->files->put($path, $generatedRoutes);
    }

    public function generate($group = false)
    {
        $this->prepareDomain();

        /** @var \Illuminate\Support\Collection $routePayload */
        $routePayload = $this->getRoutePayload($group);

        $urlGenerator = app('url');
        $defaultParameters = method_exists($urlGenerator, 'getDefaultParameters') ? $urlGenerator->getDefaultParameters() : [];

        $routePayload->pluck('uri')->map(function($data, $key) use($defaultParameters){
            dd($data, $key);
        });

        $stub = $this->files->get(__DIR__.'/stubs/ziggy.routes.d.ts.stub');
    }

    private function prepareDomain()
    {
        $url = url('/');
        $parsedUrl = parse_url($url);

        $this->baseUrl = $url . '/';
        $this->baseProtocol = array_key_exists('scheme', $parsedUrl) ? $parsedUrl['scheme'] : 'http';
        $this->baseDomain = array_key_exists('host', $parsedUrl) ? $parsedUrl['host'] : '';
        $this->basePort = array_key_exists('port', $parsedUrl) ? $parsedUrl['port'] : 'false';
    }

    public function getRoutePayload($group = false)
    {
        return RoutePayload::compile($this->router, $group);
    }

    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
        return $path;
    }
}
