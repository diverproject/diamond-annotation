<?php

namespace diamond\annotation;

abstract class AbstractAnnotation implements Annotation
{
	public const NATIVE_ANNOTATIONS_TYPE = [
		AuthorAnnotation::class,
		LinkAnnotation::class,
		SeeAnnotation::class,
		ParameterAnnotation::class,
		ReturnAnnotation::class,
		VarAnnotation::class,
		ThrowsAnnotation::class,
	];

	private $type;

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type): void
	{
		$this->type = $type;
	}

	public function isNative(): bool
	{
		return array_search(self::class, self::NATIVE_ANNOTATIONS_TYPE) !== false;
	}

	public static function getDeclaredClassName(string $classname): string
	{
		$declaredClassnames = get_declared_classes();

		foreach ($declaredClassnames as $declaredClassname)
			if (nameOf($declaredClassname) === $classname)
				return $declaredClassname;

		return $classname;
	}
}
