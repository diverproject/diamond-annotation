<?php

namespace diamond\annotation;

use diamond\lang\StringParser;
use diamond\lang\utils\GlobalFunctions;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

GlobalFunctions::load();

class AnnotationParser
{
	public const NATIVE_TYPES = ['bool', 'int', 'integer', 'float', 'double', 'string', 'array', 'resource', 'null'];

	private static $nativeAnnotations = [
		'author' => AuthorAnnotation::class,
		'link' => LinkAnnotation::class,
		'see' => SeeAnnotation::class,
		'var' => VarAnnotation::class,
		'param' => ParameterAnnotation::class,
		'return' => ReturnAnnotation::class,
		'throws' => ThrowsAnnotation::class,
	];
	private static $customAnnotations = [];

	private $annotationClass;
	private $annotationMethods;
	private $annotationProperties;

	private function __construct()
	{
		$this->annotationClass = new AnnotationClass();
		$this->annotationMethods = [];
		$this->annotationProperties = [];
	}

	public function getAnnotationClass(): AnnotationClass
	{
		return $this->annotationClass;
	}

	public function getAnnotationMethods(): array
	{
		return $this->annotationMethods;
	}

	/**
	 * @return AnnotationProperty[]
	 */
	public function getAnnotationProperties(): array
	{
		return $this->annotationProperties;
	}

	public static function registerClassName(string $class_name): bool
	{
		if (!class_exists($class_name))
			return false;

		$name = nameOf($class_name);
		self::$customAnnotations[$name] = $class_name;
		return true;
	}

	public static function registerObject(object $object): bool
	{
		return $object instanceof Annotation && self::registerClassName(get_class($object));
	}

	public static function parseObject(object $object): AnnotationParser
	{
		$reflection = new ReflectionClass(get_class($object));
		return self::parseReflectionClass($reflection);
	}

	public static function parseClassName(string $class_name): AnnotationParser
	{
		if (!class_exists($class_name))
			return false;

		$reflection = new ReflectionClass($class_name);
		return self::parseReflectionClass($reflection);
	}

	public static function parseReflectionClass(ReflectionClass $reflection): AnnotationParser
	{
		$annotation = new AnnotationParser();
		$properties = $reflection->getProperties();
		$methods = $reflection->getMethods();

		foreach ($properties as $property)
			if (($propertyAnnotation = self::parseProperty($property)) !== null)
				$annotation->annotationProperties[$propertyAnnotation->getName()] = $propertyAnnotation;

		foreach ($methods as $method)
			if (($methodAnnotation = self::parseMethod($method)) !== null)
				$annotation->annotationMethods[$methodAnnotation->getName()] = $methodAnnotation;

		foreach (self::parseDocumentation($reflection->getDocComment(), $reflection) as $classAnnotation)
			$annotation->annotationClass->addAnnotation($classAnnotation);

		return $annotation;
	}

	public static function parseProperty(ReflectionProperty $reflection): AnnotationProperty
	{
		$annotationProperty = new AnnotationProperty();
		$annotationProperty->setName($reflection->getName());
		$annotationProperty->setStatic($reflection->isStatic());
		$annotations = self::parseDocumentation($reflection->getDocComment(), $reflection);

		foreach ($annotations as $annotation)
			$annotationProperty->addAnnotation($annotation, $reflection);

		return $annotationProperty;
	}

	public static function parseMethod(ReflectionMethod $reflection): AnnotationMethod
	{
		$annotationMethod = new AnnotationMethod();
		$annotationMethod->setName($reflection->getName());
		$annotationMethod->setStatic($reflection->isStatic());
		$annotationMethod->setDescription(self::parseTextDocumentation($reflection));
		$annotations = self::parseDocumentation($reflection->getDocComment(), $reflection);

		foreach ($annotations as $annotation)
			$annotationMethod->addAnnotation($annotation, $reflection);

		return $annotationMethod;
	}

	/**
	 * @param ReflectionClass|ReflectionMethod|ReflectionProperty $reflection
	 * @return string
	 */
	public static function parseTextDocumentation(object $reflection): string
	{
		$output = null;
		$docComment = $reflection->getDocComment();
		$regex = strpos($docComment, '@') !== false ?
		'/(?P<description>\b[A-Za-z](.*?))(?:\s+\*\s+\@)/msux' :
		'/(?P<description>\b[A-Za-z](.*?))(?:\*\/)/suxm';

		preg_match_all($regex, $docComment, $output, PREG_SET_ORDER);

		if (!isset($output[0]) || !isset($output[0]['description']))
			return '';

		$description = trim(preg_replace('/(\s+\*\s+)/', ' ', $output[0]['description']));

		return $description;
	}

	/**
	 *
	 * @param string $documentation
	 * @param ReflectionClass|ReflectionMethod|ReflectionProperty $reflection
	 * @return array
	 */
	public static function parseDocumentation(string $documentation, ?object $reflection = null): array
	{
		$annotations = [];
		$documentation = utf8_encode($documentation);

		$regex = '/@
(?P<type>[\\\\\w]+(:{2})?[\\\\\w]+(\(\))?)
(?:\s|\()
(?P<data>(?:[\\\\\|\$\%\/\w\s\"\<\>\_\#\=\-\.\'\{\}:;,\*\(\)\[\]]*[^\R\*\s\/\)]))?
(?:\s | $|\))/mxs';
		$annotationsParsed = [];
		$hasAnnotations = preg_match_all($regex, $documentation, $annotationsParsed, PREG_SET_ORDER);

		if ($hasAnnotations)
		foreach ($annotationsParsed as &$annotationParsed)
		{
			foreach (array_keys($annotationParsed) as $key)
				if (is_int($key))
					unset($annotationParsed[$key]);

			if (!isset($annotationParsed['type']))
				continue;

			if (!isset($annotationParsed['data']))
				$annotationParsed['data'] = null;
			else
				$annotationParsed['data'] = preg_replace('/(\s)/', ' ', str_replace('*', '', $annotationParsed['data']));

			$annotationParsed['type'] = trim($annotationParsed['type']);
			$annotationParsed['data'] = trim($annotationParsed['data']);

			if (($annotation = self::newAnnotation($annotationParsed['type'])) !== null)
			{
				if (!StringParser::isEmpty($annotationParsed['data']))
					$annotation->parse($annotationParsed['data'], $documentation, $reflection);

				$annotations[] = $annotation;
			}
		}

		return $annotations;
	}

	public static function newAnnotation(string $type): ?Annotation
	{
		if (isset(self::$nativeAnnotations[$type]))
			$class_name = self::$nativeAnnotations[$type];
		else if (isset(self::$customAnnotations[$type]))
			$class_name = self::$customAnnotations[$type];

		return isset($class_name) && class_exists($class_name) ? new $class_name() : null;
	}


	public static function classnameOf(string $class_name): ?string
	{
		if ($class_name{0} === '\\')
			$class_name = substr($class_name, 1);

		class_exists($class_name); // Force load class if not loaded yet
		$declaredClassnames = get_declared_classes();

		foreach ($declaredClassnames as $declaredClassname)
		{
			if ((StringParser::contains($class_name, '\\') && $declaredClassname === $class_name) ||
				(!StringParser::contains($class_name, '\\') && nameOf($declaredClassname) === $class_name))
				return $declaredClassname;
		}

		return null;
	}

	public static function getMultiTypes(string $types): array
	{
		$array = explode('|', $types);

		foreacH ($array as &$type)
			if (strlen($type) > 0 && $type{0} === '\\')
				$type = substr($type, 1);

		return $array;
	}

	public static function hasMultiTypesNull(string $types): bool
	{
		$types = self::getMultiTypes($types);
		return in_array('null', $types) || in_array('NULL', $types);
	}

	public static function getFirstMultiType(string $types, bool $onlyClasses = true): ?string
	{
		foreach (self::getMultiTypes($types) as $class_name)
			if (strlen($class_name) > 0 && $class_name !== 'NULL')
				if ((!$onlyClasses && self::isNativeType($class_name)) || ($class_name = self::classnameOf($class_name)) !== null)
					return $class_name;

		return null;
	}

	public static function isNativeType(string $type): bool
	{
		return array_search(strtolower($type), self::NATIVE_TYPES) !== false;
	}
}

