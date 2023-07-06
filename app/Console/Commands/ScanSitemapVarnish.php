<?php

namespace App\Console\Commands;

use Domains\Varnish\Controllers\VarnishController;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use JetBrains\PhpStorm\ArrayShape;

class ScanSitemapVarnish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varnish:sitemap {path} {mask?} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan sitemap for update varnish cache.';

    /**
     * Execute the console command.
     *
     * @throws GuzzleException
     */
    #[ArrayShape(['status' => "int"])] public function handle(): void
    {
        $this->info((string) VarnishController::scanSitemap(
            (string) $this->argument('path'),
            (string) $this->argument('mask'),
        ));
    }
}
