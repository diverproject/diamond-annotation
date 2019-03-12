<?php

namespace diamond\annotation;

class ThrowsAnnotation extends AbstractAnnotation
{
	private $description;

	public function __constrcut()
	{
		$this->setDescription('');
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	public function parse(string $data, string $documentation, object $reflection): void
	{
		$exploded = explode(' ', $data);

		if (isset($exploded[0])) $this->setType(self::getDeclaredClassName($exploded[0]));
		if (isset($exploded[1])) $this->setDescription(implode(' ', array_slice($exploded, 1)));
	}
}

