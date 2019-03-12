<?php

namespace diamond\annotation;

class CustomAnnotation extends AbstractAnnotation
{
	protected $properties;

	public function parse(string $data, string $documentation, object $reflection): void
	{
		if ($data{0} == '{' && $data{-1} == '}')
		{
			$this->properties = json_decode($data, true);
			parent::setType('json');
			return;
		}

		parent::setType('simple');

		$properties = preg_split('/;|,/', $data);

		if (count($properties) > 0)
		foreach ($properties as $property)
		{
			$explode = explode('=', $property);

			switch (count($explode))
			{
				case 1:
					$this->set($explode[0], true);
					break;

				case 2:
					if (!empty($explode[0]) && !empty($explode[0]))
						$this->set($explode[0], $explode[1]);
					break;
			}
		}
	}

	public function has(string $property): bool
	{
		return isset($this->properties[$property]);
	}

	public function get(string $property): ?string
	{
		return $this->has($property) ? $this->properties[$property] : null;
	}

	private function set(string $property, $value): void
	{
		$this->properties[$property] = $value;
	}
}
