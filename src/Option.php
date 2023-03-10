<?php

namespace Ngockush\Crawler\NgockushCrawler;

use Backpack\Settings\app\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Option
{
    public static function get($name, $default = null)
    {
        $entry = static::getEntry();
        $fields = array_column(static::getAllOptions(), 'value', 'name');

        $options = array_merge($fields, json_decode($entry->value, true) ?? []);

        return isset($options[$name]) ? $options[$name] : $default;
    }

    // public static function set($name, $value)
    // {
    //     $entry = static::getEntry();
    //     $fields = array_column(static::getAllOptions(), 'value', 'name');

    //     $options = array_merge($fields, json_decode($entry->value, true) ?? []);

    //     $options[$name] = $value;

    //     return Setting::updateOrCreate([
    //         'key' => 'ngockush/ngockush-crawler.options',
    //     ], [
    //         'name' => 'Options',
    //         'field' => json_encode(['name' => 'value', 'type', 'hidden']),
    //         'value' => json_encode($options),
    //         'group' => 'crawler',
    //         'active' => false
    //     ]);
    // }

    public static function getEntry()
    {
        return Setting::firstOrCreate([
            'key' => 'ngockush/ngockush-crawler.options',
        ], [
            'name' => 'Options',
            'field' => json_encode(['name' => 'value', 'type', 'hidden']),
            'group' => 'crawler',
            'active' => false
        ]);
    }

    public static function getAllOptions()
    {
        $categories = Cache::remember('ophim_categories', 86400, function () {
            $data = json_decode(file_get_contents(sprintf('%s/the-loai', config('ophim_crawler.domain', 'https://anime.s2fastplayer.xyz'))), true) ?? [];
            return collect($data)->pluck('name', 'name')->toArray();
        });

        $regions = Cache::remember('ophim_regions', 86400, function () {
            $data = json_decode(file_get_contents(sprintf('%s/quoc-gia', config('ophim_crawler.domain', 'https://anime.s2fastplayer.xyz'))), true) ?? [];
            return collect($data)->pluck('name', 'name')->toArray();
        });

        $fields = [
            'episodes' => 'T???p m???i',
            'status' => 'Tr???ng th??i phim',
            'episode_time' => 'Th???i l?????ng t???p phim',
            'episode_current' => 'S??? t???p phim hi???n t???i',
            'episode_total' => 'T???ng s??? t???p phim',
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
            'type' => '?????nh d???ng phim',
            'is_shown_in_theater' => '????nh d???u phim chi???u r???p',
            'actors' => 'Di???n vi??n',
            'directors' => '?????o di???n',
            'categories' => 'Th??? lo???i',
            'regions' => 'Khu v???c',
            'tags' => 'T??? kh??a',
            'studios' => 'Studio',
        ];

        return [
            'domain' => [
                'name' => 'domain',
                'label' => 'API Domain',
                'type' => 'text',
                'value' => 'https://anime.s2fastplayer.xyz',
                'tab' => 'Setting'
            ],
            'download_image' => [
                'name' => 'download_image',
                'label' => 'T???i ???nh khi crawl',
                'type' => 'checkbox',
                'tab' => 'Image Optimize'
            ],
            'should_resize_thumb' => [
                'name' => 'should_resize_thumb',
                'label' => 'Resize ???nh thumb khi t???i v???',
                'type' => 'checkbox',
                'tab' => 'Image Optimize'
            ],
            'resize_thumb_width' => [
                'name' => 'resize_thumb_width',
                'label' => 'Chi???u r???ng ???nh thumb (px)',
                'type' => 'number',
                'default' => 200,
                'attributes' => [
                    'placeholder' => '????? tr???ng n???u mu???n gi??? nguy??n t??? l???',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-6',
                ],
                'tab' => 'Image Optimize'
            ],
            'resize_thumb_height' => [
                'name' => 'resize_thumb_height',
                'label' => 'Chi???u cao ???nh thumb (px)',
                'type' => 'number',
                'attributes' => [
                    'placeholder' => '????? tr???ng n???u mu???n gi??? nguy??n t??? l???',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-6',
                ],
                'tab' => 'Image Optimize'
            ],
            'should_resize_poster' => [
                'name' => 'should_resize_poster',
                'label' => 'Resize ???nh poster khi t???i v???',
                'type' => 'checkbox',
                'tab' => 'Image Optimize'
            ],
            'resize_poster_width' => [
                'name' => 'resize_poster_width',
                'label' => 'Chi???u r???ng ???nh poster (px)',
                'type' => 'number',
                'default' => 300,
                'attributes' => [
                    'placeholder' => '????? tr???ng n???u mu???n gi??? nguy??n t??? l???',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-6',
                ],
                'tab' => 'Image Optimize'
            ],
            'resize_poster_height' => [
                'name' => 'resize_poster_height',
                'label' => 'Chi???u cao ???nh poster (px)',
                'type' => 'number',
                'attributes' => [
                    'placeholder' => '????? tr???ng n???u mu???n gi??? nguy??n t??? l???',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-6',
                ],
                'tab' => 'Image Optimize'
            ],
            'crawler_schedule_enable' => [
                'name' => 'crawler_schedule_enable',
                'label' => '<b>B???t/T???t t??? ?????ng</b>',
                'default' => false,
                'type' => 'checkbox',
                'tab' => 'Schedule'
            ],
            'crawler_schedule_page_from' => [
                'name' => 'crawler_schedule_page_from',
                'label' => 'Trang ?????u',
                'type' => 'number',
                'default' => 1,
                'attributes' => [
                    'placeholder' => '1',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-4',
                ],
                'tab' => 'Schedule'
            ],
            'crawler_schedule_page_to' => [
                'name' => 'crawler_schedule_page_to',
                'label' => 'Trang cu???i',
                'type' => 'number',
                'default' => 2,
                'attributes' => [
                    'placeholder' => '2',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-4',
                ],
                'tab' => 'Schedule'
            ],
            'crawler_schedule_cron_config' => [
                'name'        => 'crawler_schedule_cron_config',
                'label'       => 'Cron config',
                'type'        => 'text',
                'default'     => '* * * * *',
                'hint'        => '<a target="_blank" href="https://crontab.guru/every-10-minutes">See more</a>',
                'attributes' => [
                    'placeholder' => '* * * * * *',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-4',
                ],
                'tab'   => 'Schedule'
            ],
            'crawler_schedule_excludedType' => [
                'name' => 'crawler_schedule_excludedType',
                'label' => 'B??? qua ?????nh d???ng',
                'type' => 'select_from_array',
                'options'         => ['series' => 'Phim B???', 'single' => 'Phim L???', 'hoathinh' => 'Ho???t H??nh', 'tvshows' => 'TV Shows'],
                'allows_null'     => false,
                'allows_multiple' => true,
                'tab' => 'Schedule'
            ],
            'crawler_schedule_excludedCategories' => [
                'name' => 'crawler_schedule_excludedCategories',
                'label' => 'B??? qua th??? lo???i',
                'type' => 'select_from_array',
                'options'         => $categories,
                'allows_null'     => false,
                'allows_multiple' => true,
                'tab' => 'Schedule'
            ],
            'crawler_schedule_excludedRegions' => [
                'name' => 'crawler_schedule_excludedRegions',
                'label' => 'B??? qua qu???c gia',
                'type' => 'select_from_array',
                'options'         => $regions,
                'allows_null'     => false,
                'allows_multiple' => true,
                'tab' => 'Schedule'
            ],
            'crawler_schedule_fields' => [
                'name' => 'crawler_schedule_fields',
                'label' => 'Field c???p nh???t',
                'type' => 'select_from_array',
                'default' => array_keys($fields),
                'options'         => $fields,
                'allows_null'     => false,
                'allows_multiple' => true,
                'tab' => 'Schedule'
            ],
        ];
    }
}
