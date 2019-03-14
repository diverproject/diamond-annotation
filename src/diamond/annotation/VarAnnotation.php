<?php

namespace diamond\annotation;

class VarAnnotation extends AbstractAnnotation
{
	private $static;
	private $description;
	private $acceptNull;

	public function __construct()
	{
		$this->setType('');
		$this->setStatic(false);
		$this->setDescription('');
		$this->setAcceptNull(false);
	}

	public function isNativeType(): bool
	{
		return AnnotationParser::isNativeType($this->getType());
	}

	public function isStatic(): bool
	{
		return $this->static;
	}

	public function setStatic(bool $static): void
	{
		$this->static = $static;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function setDescription(string $description): void
	{
		$this->description = $description;
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
		$this->setStatic($reflection->isStatic());

		if (($strpos = strpos($data, ' ')) !== false)
		{
			$this->setDescription(substr($data, $strpos + 1));
			$data = substr($data, 0, $strpos);
		}

		$this->setType(nvl(AnnotationParser::getFirstMultiType($data, false), 'object'));
		$this->setAcceptNull(AnnotationParser::hasMultiTypesNull($data));
	}
 }
