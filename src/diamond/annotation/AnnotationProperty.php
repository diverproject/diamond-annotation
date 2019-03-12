<?php

namespace diamond\annotation;

class AnnotationProperty
{
	private $static;
	private $name;
	private $annotations;

	public function __construct()
	{
		$this->static = false;
		$this->name = 'UNDEFINED';
		$this->annotations = [];
	}

	public function isStatic(): bool
	{
		return $this->static;
	}

	public function setStatic(bool $static): void
	{
		$this->static = $static;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $Name)
	{
		$this->name = $Name;
	}

	public function getAnnotations(): array
	{
		return $this->annotations;
	}

	public function addAnnotation(Annotation $annotation): void
	{
		$this->annotations[] = $annotation;
	}
}
