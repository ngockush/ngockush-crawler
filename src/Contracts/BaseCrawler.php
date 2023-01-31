<?php

namespace Ophim\Crawler\OphimCrawler\Contracts;

abstract class BaseCrawler
{
    protected $link;
    protected $api_key;
    protected $fields;
    protected $excludedCategories;
    protected $excludedRegions;
    protected $excludedType;
    protected $forceUpdate;

    public function __construct($link, $api_key, $fields, $excludedCategories = [], $excludedRegions = [], $excludedType = [], $forceUpdate)
    {
        $this->link = $link;
        $this->api_key = $api_key;
        $this->fields = $fields;
        $this->excludedCategories = $excludedCategories;
        $this->excludedRegions = $excludedRegions;
        $this->excludedType = $excludedType;
        $this->forceUpdate = $forceUpdate;
    }

    abstract public function handle();
}
