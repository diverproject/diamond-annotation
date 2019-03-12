<?php

namespace diamond\annotation;

class AnnotationException extends \Exception
{
	private static $customMessages = [];

	public const AE_REFLECTION_CLASS = 1;

	public function __construct(int $code)
	{
		$args = array_slice(func_get_args(), 1);
		$previous = end($args) instanceof \Throwable ? array_pop($args) : null;
		$format = self::getDefaultMessage($code);
		array_unshift($args, $format);
		$message = format($args);

		parent::__construct($message, $code, $previous);
	}

	public static function getDefaultMessage(int $code): string
	{
		if (isset(self::$customMessages[$code]))
			return self::$customMessages[$code];

		switch ($code)
		{
			case self::AE_REFLECTION_CLASS: return 'invalid reflection';
		}
	}

	public static function getCustomMessages(): array
	{
		return self::$customMessages;
	}

	public static function getCustomMessage(int $code): ?string
	{
		return isset(self::$customMessages[$code]) ? self::$customMessages[$code] : null;
	}

	public static function setCustomMessages(array $customMessages): void
	{
		self::$customMessages = $customMessages;
	}

	public static function setCustomMessage(int $code, string $customMessage): void
	{
		self::$customMessages = self::$customMessages[$code] = $customMessage;
	}
}

