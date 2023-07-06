<?php

namespace Domains\Varnish\Controllers;

use App\Http\Controllers\Controller;

use Domains\Varnish\Models\VarnishModel;
use Domains\Varnish\Models\VarnishSitemapModel;
use Domains\Varnish\Observers\VarnishCrawlerObserver;
use Exception;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\DB;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\Crawler;

class VarnishController extends Controller
{
    /**
     * @return int[]
     */
    #[ArrayShape(['status' => "int"])] public static function index(): array
    {
        return ['status' => 200];
    }


    /**
     * Scan sitemap and go to links.
     *
     * @param string $link Sitemap xml file.
     * @param string $clearMask Rescan urls by mask ['/catalog/'].
     * @param array $replaceInUrl String replace path in urls
     * @return int[]
     * @throws GuzzleException
     * @throws Exception
     */
    #[ArrayShape(['status' => "int"])] public static function scanSitemap(
        string $link = 'https://www.kant.ru/sitemap.xml',
        string $clearMask = '',
        array $replaceInUrl = []
    ) : array
    {
        set_time_limit(6000);

        $client = new Client(['headers' => ['Cache-Control' => 'no-cache']]);
        $request = $client->get($link);
        $response = $request->getBody()->getContents();
        $obDocument = new \SimpleXMLElement($response);
        $result = [];
        foreach ($obDocument->sitemap as $item) {
            $result[] = new \SimpleXMLElement($client->get((string) $item->loc)->getBody()->getContents());
        }

        VarnishSitemapModel::query()->truncate();


        foreach ($result as $item) {
            $arLinks = [];
            foreach ($item->url as $url) {
                /** { @internal Relocate to another domain. } */
                $uri = (string) $url->loc;
                if(!empty($replaceInUrl)) $uri = str_replace($replaceInUrl[0], $replaceInUrl[1], $uri);

                if(!empty($uri)) {
                    $arLinks[] = ['url' => $uri, 'status' => 200];
                }
            }

            VarnishSitemapModel::insert($arLinks);
        }

        $clearMask='/product/3391004';

        /** Clear by mask */
        if(!empty($clearMask)) VarnishModel::where('url', 'like', '%' .$clearMask.'%')->delete();

        /**  Select crawled values  */
        $result = DB::select("
            SELECT * FROM varnish_sitemap
            WHERE url NOT IN (SELECT url FROM varnish)"
        );
        $arLinks = array_map(function ($value) {
            return $value->url;
        }, $result);

        /**  @todo Replace cookies to params */
        $cookieJar = CookieJar::fromArray([
            'BITRIX_SM_NEW_SITE' => '1'
        ], '.kant.ru');

        /**
         * @return Generator
         * @var array|Iterator $requestGenerator
         */
        $requestGenerator = function($searchTerms) use ($client, $cookieJar) {
            foreach($searchTerms as $searchTerm) {
                // The magic happens here, with yield key => value
                yield $searchTerm => function() use ($client, $searchTerm, $cookieJar) {
                    // Our identifier does not have to be included in the request URI or headers
                    return $client->getAsync($searchTerm, ['cookies' => $cookieJar]);
                };
            }
        };

        /**  {@internal If need scan with Crawler } */
//        foreach ($arLinks as $arLink) {
//            self::scan($arLink, 1, 1);
//        }


        foreach (array_chunk($arLinks, 20) as $arLink) {
            $pool = new Pool($client, $requestGenerator($arLink), [
                'concurrency' => 3,
                'fulfilled' => function(Response $response, $index) {
                    VarnishModel::updateOrCreate([
                        'url' => $index,
                    ], [
                        'status' => $response->getStatusCode()
                    ]);
                },
                'rejected' => function(Exception $reason, $index) {
                    VarnishModel::updateOrCreate([
                        'url' => $index
                    ], [
                        'status' => 404,
                        'content' => "Requested search term: ", $index, "\n" . $reason->getMessage()
                    ]);
                },
            ]);
            $promise = $pool->promise();
            $promise->wait();
        }

        return ['status' => 200];
    }

    /**
     * Scan link with crawler.
     *
     * @param UriInterface|string $link
     * @param int $iLimit
     * @param int $maxDepth
     * @return int[]
     */
    #[ArrayShape(['status' => "int"])] public static function scan(
        UriInterface | string $link = 'https://spa.kant.ru/catalog/',
        int $iLimit = 1000,
        int $maxDepth = 5
    ): array
    {
        $iStartFrom = 0;
        //# initiate crawler
        $obCrawler = Crawler::create(
            [
                RequestOptions::ALLOW_REDIRECTS => true,
                RequestOptions::TIMEOUT => 120,
                RequestOptions::COOKIES => new CookieJar(true, [
                    ['Name' => 'BITRIX_SM_NEW_SITE', 'Value' => 1, 'Domain' => '.kant.ru', 'Path' => '/']
                ])
            ]
        );
        $obCrawler
            ->ignoreRobots()
//                ->executeJavaScript()
            ->setMaximumDepth($maxDepth)
            ->acceptNofollowLinks()
            ->setParseableMimeTypes(['text/html', 'text/plain'])
            ->setCrawlObserver(new VarnishCrawlerObserver())
            ->setCrawlProfile(new \Spatie\Crawler\CrawlProfiles\CrawlInternalUrls($link))
            ->setMaximumResponseSize(1024 * 1024 * 2) // 2 MB maximum
            ->setTotalCrawlLimit($iLimit + $iStartFrom) // limit defines the maximal count of URLs to crawl
            ->setConcurrency(1) // all urls will be crawled one by one
            ->setDelayBetweenRequests(100)
            ->startCrawling($link);


        return ['status' => 200];
    }
}
