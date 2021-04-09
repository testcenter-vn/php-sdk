<?php

namespace Testcenter\Testcenter\Models;

class TestCampaign
{
	public $id;
	public $name;

    public function __construct($testCampaign)
    {
    	$this->id = $testCampaign->id;
    	if (isset($testCampaign->name)) {
    		$this->name = $testCampaign->name;
    	}
    }
}