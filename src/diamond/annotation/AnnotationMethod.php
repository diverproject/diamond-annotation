<?php

namespace diamond\annotation;

class AnnotationMethod
{
	private $static;
	private $name;
	private $description;
	private $annotations;
	private $parameterAnnotations;

	public function __construct()
	{
		$this->annotations = [];
		$this->parameterAnnotations = [];
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

	public function setName(string $Name): void
	{
		$this->name = $Name;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	public function getAnnotations(): array
	{
		return $this->annotations;
	}

	public function getParameterAnnotations(): array
	{
		return $this->parameterAnnotations;
	}

	public function addAnnotation(AbstractAnnotation $annotation): void
	{
		if ($annotation instanceof ParameterAnnotation)
			$this->parameterAnnotations[$annotation->getName()] = $annotation;

		$this->annotations[] = $annotation;
	}
}
