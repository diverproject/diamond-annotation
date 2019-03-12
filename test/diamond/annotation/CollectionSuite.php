<?php

namespace test\diamond\annotation;

use PHPUnit\Framework\TestSuite;

/**
 * Static test suite.
 */
class CollectionSuite extends TestSuite
{
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct()
	{
		$this->setName('CollectionSuite');
	}

	/**
	 * Creates the suite.
	 */
	public static function suite()
	{
		return new self();
	}
}

