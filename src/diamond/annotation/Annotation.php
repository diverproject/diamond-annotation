<?php

namespace diamond\annotation;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

interface Annotation
{
	/**
	 *
	 * @param string $data
	 * @param string $documentation
	 * @param ReflectionMethod|ReflectionClass|ReflectionProperty $reflection
	 */
	public function parse(string $data, string $documentation, object $reflection): void;
	public function isNative(): bool;
}
