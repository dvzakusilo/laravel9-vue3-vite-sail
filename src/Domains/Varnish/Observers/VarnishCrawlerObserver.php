<?php

namespace Domains\Varnish\Observers;


use Domains\Varnish\Models\VarnishModel;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class VarnishCrawlerObserver extends CrawlObserver
{
    //
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null): void
    {
// Create records
        VarnishModel::updateOrCreate([
            'url' => $url,
        ], [
            'status' => $response->getStatusCode()
        ]);
    }

    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null): void
    {
        // TODO: Implement crawlFailed() method.
    }
}
