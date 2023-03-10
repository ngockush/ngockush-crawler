<?php

namespace Ngockush\Crawler\NgockushCrawler\Controllers;


use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Ngockush\Crawler\NgockushCrawler\Crawler;
use Ophim\Core\Models\Movie;

/**
 * Class CrawlController
 * @package Ngockush\Crawler\NgockushCrawler\Controllers
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CrawlController extends CrudController
{
    public function fetch(Request $request)
    {
        try {
            $data = collect();

            $request['link'] = preg_split('/[\n\r]+/', $request['link']);

            foreach ($request['link'] as $link) {
                if (preg_match('/(.*?)(\/movies\/detail\/)(.*?)/', $link)) {
                    $link = sprintf('%s/movies/detail/%s?api_key=QuaWpJdasPfXSlRdqjltMzAlMkYwMSUyRjIwMjM=', config('ophim_crawler.domain', 'https://anime.s2fastplayer.xyz'), explode('movies/detail/', $link)[1]);
                    $response = json_decode(file_get_contents($link), true);
                    $data->push(collect($response['movie'])->only('name', 'slug')->toArray());
                } else {
                    for ($i = $request['from']; $i <= $request['to']; $i++) {
                        $response = json_decode(Http::timeout(30)->get($link, [
                            'page' => $i,
                            'api_key' => 'QuaWpJdasPfXSlRdqjltMzAlMkYwMSUyRjIwMjM='
                        ]), true);
                        if ($response['status']) {
                            $data->push(...$response['items']);
                        }
                    }
                }
            }

            return $data->shuffle();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function showCrawlPage(Request $request)
    {
        $categories = Cache::remember('ophim_categories', config('ophim_cache_ttl', 5 * 60), function () {
            $data = json_decode(file_get_contents(sprintf('%s/the-loai', config('ophim_crawler.domain', 'https://anime.s2fastplayer.xyz'))), true) ?? [];
            return collect($data)->pluck('name', 'name')->toArray();
        });

        $regions = Cache::remember('ophim_regions', config('ophim_cache_ttl', 5 * 60), function () {
            $data = json_decode(file_get_contents(sprintf('%s/quoc-gia', config('ophim_crawler.domain', 'https://anime.s2fastplayer.xyz'))), true) ?? [];
            return collect($data)->pluck('name', 'name')->toArray();
        });

        $fields = $this->movieUpdateOptions();

        return view('ngockush-crawler::crawl', compact('fields', 'regions', 'categories'));
    }

    public function crawl(Request $request)
    {
        $pattern = sprintf('%s/movies/detail/{slug}?api_key=QuaWpJdasPfXSlRdqjltMzAlMkYwMSUyRjIwMjM=', config('ophim_crawler.domain', 'https://anime.s2fastplayer.xyz'));
        try {
            $link = str_replace('{slug}', $request['slug'], $pattern);
            $crawler = (new Crawler($link, request('fields', []), request('excludedCategories', []), request('excludedRegions', []), request('excludedType', []), request('forceUpdate', false)))->handle();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'wait' => false], 500);
        }
        return response()->json(['message' => 'OK', 'wait' => $crawler ?? true]);
    }

    protected function movieUpdateOptions(): array
    {
        return [
            'Ti???n ????? phim' => [
                'episodes' => 'T???p m???i',
                'status' => 'Tr???ng th??i phim',
                'episode_time' => 'Th???i l?????ng t???p phim',
                'episode_current' => 'S??? t???p phim hi???n t???i',
                'episode_total' => 'T???ng s??? t???p phim',
            ],
            'Th??ng tin phim' => [
                'name' => 'T??n phim',
                'origin_name' => 'T??n g???c phim',
                'content' => 'M?? t??? n???i dung phim',
                'thumb_url' => '???nh Thumb',
                'poster_url' => '???nh Poster',
                'trailer_url' => 'Trailer URL',
                'quality' => 'Ch???t l?????ng phim',
                'language' => 'Ng??n ng???',
                'notify' => 'N???i dung th??ng b??o',
                'showtimes' => 'Gi??? chi???u phim',
                'publish_year' => 'N??m xu???t b???n',
                'is_copyright' => '????nh d???u c?? b???n quy???n',
            ],
            'Ph??n lo???i' => [
                'type' => '?????nh d???ng phim',
                'is_shown_in_theater' => '????nh d???u phim chi???u r???p',
                'actors' => 'Di???n vi??n',
                'directors' => '?????o di???n',
                'categories' => 'Th??? lo???i',
                'regions' => 'Khu v???c',
                'tags' => 'T??? kh??a',
                'studios' => 'Studio',
            ]
        ];
    }

    public function getMoviesFromParams(Request $request)
    {
        $field = explode('-', request('params'))[0];
        $val = explode('-', request('params'))[1];
        if (!$val) {
            return Movie::where($field, $val)->orWhere($field, NULL)->get();
        } else {
            return Movie::where($field, $val)->get();
        }
    }
}
