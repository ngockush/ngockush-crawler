<?php

namespace Ophim\Crawler\OphimCrawler;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as SP;
use Ophim\Crawler\OphimCrawler\Console\CrawlerScheduleCommand;
use Ophim\Crawler\OphimCrawler\Option;

class OphimCrawlerServiceProvider extends SP
{
    /**
     * Get the policies defined on the provider.
     *
     * @return array
     */
    public function policies()
    {
        return [];
    }

    public function register()
    {

        config(['plugins' => array_merge(config('plugins', []), [
            'hacoidev/ngockush-crawler' =>
            [
                'name' => 'Ngockush Crawler',
                'package_name' => 'hacoidev/ngockush-crawler',
                'icon' => 'la la-hand-grab-o',
                'entries' => [
                    ['name' => 'Crawler', 'icon' => 'la la-hand-grab-o', 'url' => backpack_url('/plugin/ngockush-crawler')],
                    ['name' => 'Option', 'icon' => 'la la-cog', 'url' => backpack_url('/plugin/ngockush-crawler/options')],
                ],
            ]
        ])]);

        config(['logging.channels' => array_merge(config('logging.channels', []), [
            'ngockush-crawler' => [
                'driver' => 'daily',
                'path' => storage_path('logs/hacoidev/ngockush-crawler.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 7,
            ],
        ])]);

        config(['ophim.updaters' => array_merge(config('ophim.updaters', []), [
            [
                'name' => 'Ngockush Crawler',
                'handler' => 'Ophim\Crawler\OphimCrawler\Crawler'
            ]
        ])]);
    }

    public function boot()
    {
        $this->commands([
            CrawlerScheduleCommand::class,
        ]);

        $this->app->booted(function () {
            $this->loadScheduler();
        });

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ngockush-crawler');
    }

    protected function loadScheduler()
    {
        $schedule = $this->app->make(Schedule::class);
        $schedule->command('ophim:plugins:ngockush-crawler:schedule')->cron(Option::get('crawler_schedule_cron_config', '*/10 * * * *'))->withoutOverlapping();
    }
}
