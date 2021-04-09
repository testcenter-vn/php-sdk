<?php

namespace Testcenter\Testcenter\Models;

class TestCampaignStatus
{
    public $canJoinTest;
    public $name;
    public $startAt;
    public $endAt;
    const ALLOW_FILL_ATTR = [
        'canJoinTest',
        'name',
        'startAt',
        'endAt',
    ];
    public function __construct($object)
    {
        foreach ($object as $key => $value) {
            if (!in_array($key, self::ALLOW_FILL_ATTR)) {
                continue;
            }
            $this->{$key} = $value;
        }
    }
}

