<?php

namespace Testcenter\Testcenter\Models;

class Test
{
    public $name;
    public $id;
    public $categoryName;

	public function __construct($object)
	{
		$this->name = isset($object->name) ? $object->name : null;
		$this->id = isset($object->id) ? $object->id : null;
		$this->categoryName = isset($object->test_category_name) ? $object->test_category_name : null;
	}
}
