<?php

namespace diamond\annotation;

class LinkAnnotation extends AbstractAnnotation
{
	private $link;

	public function __construct()
	{
		$this->setType('');
		$this->setLink('');
	}

	public function getLink(): string
	{
		return $this->link;
	}

	public function setLink(string $link): void
	{
		$this->link = $link;
	}

	public function parse(string $data, string $documentation, object $reflection): void
	{
		$filter = filter_var($data, FILTER_VALIDATE_URL);

		$this->setLink($data);
		$this->setType($filter ? 'url' : 'unknow');
	}
}

