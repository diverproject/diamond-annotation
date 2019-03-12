<?php

namespace diamond\annotation;

class ParameterAnnotation extends AbstractAnnotation
{
	private $name;
	private $description;
	private $acceptedTypes;
	private $acceptNull;

	public function __construct()
	{
		$this->setType('');
		$this->setName('');
		$this->setDescription('');
		$this->setAcceptedTypes([]);
		$this->setAcceptNull(true);
	}

	public function getAcceptedTypes(): array
	{
		return $this->acceptedTypes;
	}

	public function setAcceptedTypes(array $acceptedTypes): void
	{
		$this->acceptedTypes = $acceptedTypes;
	}

	public function isAcceptNull(): bool
	{
		return $this->acceptNull;
	}

	public function setAcceptNull(bool $acceptNull): void
	{
		$this->acceptNull = $acceptNull;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
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

		if (isset($exploded[0]))
		{
			$types = $exploded[0];
			$declaredClassName = AnnotationParser::getFirstMultiType($types, false);

			if ($declaredClassName !== null)
				$this->setType($declaredClassName);

			$this->setAcceptNull(AnnotationParser::hasMultiTypesNull($types));
			$this->setAcceptedTypes(AnnotationParser::getMultiTypes($types));
		}

		if (isset($exploded[1])) $this->setName(trim($exploded[1], '$'));
		if (isset($exploded[2])) $this->setDescription(implode(' ', array_slice($exploded, 2)));
	}
}
