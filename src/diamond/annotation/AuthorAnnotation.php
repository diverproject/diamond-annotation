<?php

namespace diamond\annotation;

class AuthorAnnotation extends AbstractAnnotation
{
	private $authorName;

	public function __construct()
	{
		$this->setType('');
		$this->setAuthorName('');
	}

	public function getAuthorName(): string
	{
		return $this->authorName;
	}

	public function setAuthorName(string $authorName): void
	{
		$this->authorName = $authorName;
	}

	public function parse(string $data, string $documentation, object $reflection): void
	{
		$this->setType(strpos($data, ' ') !== false ? 'fullname' : 'nickname');
		$this->setAuthorName($data);
	}
}

