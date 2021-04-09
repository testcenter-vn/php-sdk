<?php

namespace Testcenter\Testcenter\Models;

class PublicTest
{
	public $name;
	public $type;
	public $description;
	public $thumbnail_image;

	public function __construct($object)
	{
		foreach ($object as $key => $value) {
			$this->{$key} = $value;
		}
	}
}
