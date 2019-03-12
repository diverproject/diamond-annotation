<?php

namespace diamond\annotation;

class ReturnAnnotation extends AbstractAnnotation
{
	private $description;
	private $acceptedTypes;
	private $acceptNull;

	public function __construct()
	{
		$this->setType('');
		$this->setDescription('');
		$this->setAcceptedTypes([]);
		$this->setAcceptNull(true);
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function setDescription(string $description): void
	{
		$this->description = $description;
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

	public function parse(string $data, string $documentation, object $reflection): void
	{
		if (($strpos = strpos($data, ' ')) !== false)
		{
			$this->setDescription(substr($data, $strpos + 1));
			$data = substr($data, 0, $strpos);
		}

		$declaredClassName = AnnotationParser::getFirstMultiType($data, false);

		if ($declaredClassName !== null)
			$this->setType($declaredClassName);

		$this->setAcceptNull(AnnotationParser::hasMultiTypesNull($data));
		$this->setAcceptedTypes(AnnotationParser::getMultiTypes($data));
	}
}

