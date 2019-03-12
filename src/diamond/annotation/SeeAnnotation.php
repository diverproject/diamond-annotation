<?php

namespace diamond\annotation;

class SeeAnnotation extends AbstractAnnotation
{
	private $class;
	private $description;

	public function __construct()
	{
		$this->setType('');
		$this->setClass(false);
		$this->setDescription('');
	}

	public function isClass(): bool
	{
		return $this->class != null && $this->class;
	}

	public function isMethod(): bool
	{
		return $this->class != null && !$this->class;
	}

	public function setClass(bool $class): void
	{
		$this->class = $class;
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
		$this->class = strpos($data, '::') === false && strpos($data, '()') === false;
		$this->setType($this->isClass() ? 'class' : 'method');
		$this->setDescription($data);
	}
}

