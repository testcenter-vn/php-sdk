<?php

namespace Testcenter\Testcenter\Models;

class TestCampaign
{
    public $id;
    public $name;
    public $category;
    public $startAt;
    public $endAt;
    public $note;
    public $editUrl;

    public function __construct($testCampaign)
    {
        $this->id = $testCampaign->id;
        if (isset($testCampaign->name)) {
            $this->name = $testCampaign->name;
        }
        if (isset($testCampaign->category)) {
            $this->category = $testCampaign->category;
        }
        if (isset($testCampaign->start_at)) {
            $this->startAt = $testCampaign->start_at;
        }
        if (isset($testCampaign->end_at)) {
            $this->endAt = $testCampaign->end_at;
        }
        if (isset($testCampaign->note)) {
            $this->note = $testCampaign->note;
        }
        if (isset($testCampaign->edit_url)) {
            $this->editUrl = $testCampaign->edit_url;
        }
    }
}