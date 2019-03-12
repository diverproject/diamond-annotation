<?php

namespace diamond\annotation;

class AnnotationClass
{
	private $annotations;

	public function __construct()
	{
		$this->annotations = [];
	}

	public function getAnnotations(): array
	{
		return $this->annotations;
	}

	public function addAnnotation(AbstractAnnotation $annotation): void
	{
		if (($annotation instanceof CustomAnnotation) ||
			($annotation instanceof AuthorAnnotation) ||
			($annotation instanceof SeeAnnotation) ||
			($annotation instanceof LinkAnnotation))
			$this->annotations[] = $annotation;
	}
}
