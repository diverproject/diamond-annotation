<?php

namespace test\diamond\annotation;

use DateTime;
use diamond\annotation\AnnotationException;
use diamond\annotation\AnnotationMethod;
use diamond\annotation\AnnotationParser;
use diamond\annotation\AnnotationProperty;
use diamond\annotation\CustomAnnotation;
use diamond\annotation\LinkAnnotation;
use diamond\annotation\ParameterAnnotation;
use diamond\annotation\ReturnAnnotation;
use diamond\annotation\SeeAnnotation;
use diamond\annotation\ThrowsAnnotation;
use diamond\annotation\VarAnnotation;

class AnnotationParserTest extends DiamondCollectionTest
{
	private $parser;

	protected function setUp()
	{
		parent::setUp();
		AnnotationParser::registerClassName(CustomAnnotation::class);
		$this->parser = AnnotationParser::parseObject(new AnnotationExperimental());
	}

	protected function tearDown()
	{
		$this->experimental = null;
		parent::tearDown();
	}

	public function testAnnotationsClass()
	{
		$this->assertTrue(true);
	}

	public function testAnnotationsMethods()
	{
		$annotationMethods = $this->parser->getAnnotationMethods();
		$this->assertFalse($annotationMethods === null);

		$this->assertTrue(isset($annotationMethods[($annotationName = 'privateMethod')]));
		{
			$privateMethod = $annotationMethods[$annotationName];
			$this->assertTrue($privateMethod instanceof AnnotationMethod);
			$this->assertFalse($privateMethod->isStatic());
			$this->assertEquals('privateMethod', $privateMethod->getName());
			$this->assertEquals('Privated Method without annotations.', $privateMethod->getDescription());
			$this->assertEquals(0, count($privateMethod->getAnnotations()));
			$this->assertEquals(0, count($privateMethod->getParameterAnnotations()));
		}

		$this->assertTrue(isset($annotationMethods[($annotationName = 'protectedMethod')]));
		{
			$privateMethod = $annotationMethods[$annotationName];
			$this->assertTrue($privateMethod instanceof AnnotationMethod);
			$this->assertFalse($privateMethod->isStatic());
			$this->assertEquals('protectedMethod', $privateMethod->getName());
			$this->assertEquals('Protected method receiving parameters and return something. And a second line to increase amount of lines.', $privateMethod->getDescription());
			$this->assertEquals(4, count($privateMethod->getAnnotations()));
			$this->assertEquals(3, count($privateMethod->getParameterAnnotations()));

			$this->assertTrue(isset($privateMethod->getParameterAnnotations()['number']));
			$this->assertTrue(isset($privateMethod->getParameterAnnotations()['decimal']));
			$this->assertTrue(isset($privateMethod->getParameterAnnotations()['enabled']));

			$this->assertTrue(isset($privateMethod->getAnnotations()[0]));
			$this->assertTrue(isset($privateMethod->getAnnotations()[1]));
			$this->assertTrue(isset($privateMethod->getAnnotations()[2]));
			$this->assertTrue(isset($privateMethod->getAnnotations()[3]));

			$this->assertEquals($privateMethod->getAnnotations()[0], $privateMethod->getParameterAnnotations()['number']);
			$this->assertEquals($privateMethod->getAnnotations()[1], $privateMethod->getParameterAnnotations()['decimal']);
			$this->assertEquals($privateMethod->getAnnotations()[2], $privateMethod->getParameterAnnotations()['enabled']);

			$parameterAnnotation = $privateMethod->getParameterAnnotations()['number'];
			$this->assertTrue($parameterAnnotation instanceof ParameterAnnotation);
			$this->assertEquals('int', $parameterAnnotation->getType());
			$this->assertEquals('number', $parameterAnnotation->getName());
			$this->assertEquals('parameter of int type.', $parameterAnnotation->getDescription());
			$parameterAnnotation->isAcceptNull();

			$parameterAnnotation = $privateMethod->getParameterAnnotations()['decimal'];
			$this->assertTrue($parameterAnnotation instanceof ParameterAnnotation);
			$this->assertEquals('float', $parameterAnnotation->getType());
			$this->assertEquals('decimal', $parameterAnnotation->getName());
			$this->assertEquals('parameter of float type.', $parameterAnnotation->getDescription());

			$parameterAnnotation = $privateMethod->getParameterAnnotations()['enabled'];
			$this->assertTrue($parameterAnnotation instanceof ParameterAnnotation);
			$this->assertEquals('bool', $parameterAnnotation->getType());
			$this->assertEquals('enabled', $parameterAnnotation->getName());
			$this->assertEquals('parameter of boolean type.', $parameterAnnotation->getDescription());

			$returnAnnotation = $privateMethod->getAnnotations()[3];
			$this->assertTrue($returnAnnotation instanceof ReturnAnnotation);
			$this->assertEquals(AnnotationException::class, $returnAnnotation->getType());
			$this->assertEquals('return a exception created.', $returnAnnotation->getDescription());
			$this->assertEquals(2, count($returnAnnotation->getAcceptedTypes()));
			$this->assertEquals([AnnotationException::class, DateTime::class], $returnAnnotation->getAcceptedTypes());
		}

		$this->assertTrue(isset($annotationMethods[($annotationName = 'publicMethod')]));
		{
			$publicMethod = $annotationMethods[$annotationName];
			$this->assertTrue($publicMethod instanceof AnnotationMethod);
			$this->assertFalse($publicMethod->isStatic());
			$this->assertEquals('publicMethod', $publicMethod->getName());
			$this->assertEquals('Public method for annother annotations not used on <code>privateMethod()</code> and <code>protectedMethod()</code>.', $publicMethod->getDescription());
			$this->assertEquals(4, count($publicMethod->getAnnotations()));
			$this->assertEquals(0, count($publicMethod->getParameterAnnotations()));

			$customAnnotation = $publicMethod->getAnnotations()[0];
			$this->assertTrue($customAnnotation instanceof CustomAnnotation);
			$this->assertEquals('json', $customAnnotation->getType());
			$this->assertEquals(23, $customAnnotation->get('age'));
			$this->assertEquals('Andrew Mello', $customAnnotation->get('name'));

			$throwsAnnotation = $publicMethod->getAnnotations()[1];
			$this->assertTrue($throwsAnnotation instanceof ThrowsAnnotation);
			$this->assertEquals('AnnotationException', nameOf($throwsAnnotation->getType()));
			$this->assertEquals('exception that can be throws but in this case always.', $throwsAnnotation->getDescription());

			$seeAnnotation = $publicMethod->getAnnotations()[2];
			$this->assertTrue($seeAnnotation instanceof SeeAnnotation);
			$this->assertFalse($seeAnnotation->isClass());
			$this->assertEquals('method', $seeAnnotation->getType());
			$this->assertEquals('\diamond\annotation\Annotation::parseClass() see static method of Annotation class.', $seeAnnotation->getDescription());

			$linkAnnotation = $publicMethod->getAnnotations()[3];
			$this->assertTrue($linkAnnotation instanceof LinkAnnotation);
			$this->assertEquals('url', nameOf($linkAnnotation->getType()));
			$this->assertEquals('https://libs.diverproject.org/php-annotation', $linkAnnotation->getLink());
		}
	}

	public function testAnnotationsProperties()
	{
		$annotationProperties = $this->parser->getAnnotationProperties();
		$this->assertEquals(3, count($annotationProperties));
		$this->assertFalse($annotationProperties === null);

		$this->assertTrue(isset($annotationProperties[($propertyName = 'privateObjectScope')]));
		{
			$annotation = $annotationProperties[$propertyName];
			$this->assertTrue($annotation instanceof AnnotationProperty);
			$this->assertFalse($annotation->isStatic());
			$this->assertEquals($annotation->getName(), $propertyName);
			$this->assertEquals(2, count($annotation->getAnnotations()));

			$varAnnotation = $annotation->getAnnotations()[0];
			$this->assertTrue($varAnnotation instanceof VarAnnotation);
			$this->assertFalse($varAnnotation->isStatic());
			$this->assertFalse($varAnnotation->isAcceptNull());
			$this->assertEquals(AnnotationProperty::class, $varAnnotation->getType());
			$this->assertEquals('object property of type AnnotationProperty.', $varAnnotation->getDescription());

			$customAnnotation = $annotation->getAnnotations()[1];
			$this->assertTrue($customAnnotation instanceof CustomAnnotation);
			$this->assertEquals('experimental', $customAnnotation->get('name'));
			$this->assertEquals('example', $customAnnotation->get('type'));
		}

		$this->assertTrue(isset($annotationProperties[($propertyName = 'null')]));
		{
			$annotation = $annotationProperties[$propertyName];
			$this->assertTrue($annotation instanceof AnnotationProperty);
			$this->assertFalse($annotation->isStatic());
			$this->assertEquals($annotation->getName(), $propertyName);
			$this->assertEquals(1, count($annotation->getAnnotations()));

			$varAnnotation = $annotation->getAnnotations()[0];
			$this->assertTrue($varAnnotation instanceof VarAnnotation);
			$this->assertFalse($varAnnotation->isStatic());
			$this->assertTrue($varAnnotation->isAcceptNull());
			$this->assertFalse($varAnnotation->isNativeType());
			$this->assertEquals(AnnotationProperty::class, $varAnnotation->getType());
			$this->assertEquals('object property of type AnnotationProperty nullable.', $varAnnotation->getDescription());
		}

		$this->assertTrue(isset($annotationProperties[($propertyName = 'native')]));
		{
			$annotation = $annotationProperties[$propertyName];
			$this->assertTrue($annotation instanceof AnnotationProperty);
			$this->assertFalse($annotation->isStatic());
			$this->assertEquals($annotation->getName(), $propertyName);
			$this->assertEquals(1, count($annotation->getAnnotations()));

			$varAnnotation = $annotation->getAnnotations()[0];
			$this->assertTrue($varAnnotation instanceof VarAnnotation);
			$this->assertFalse($varAnnotation->isStatic());
			$this->assertFalse($varAnnotation->isAcceptNull());
			$this->assertTrue($varAnnotation->isNativeType());
			$this->assertEquals('int', $varAnnotation->getType());
			$this->assertEquals('a native property.', $varAnnotation->getDescription());
		}
	}

	public function testUtils()
	{
		$this->assertEquals(self::class, AnnotationParser::classnameOf(nameOf(self::class)));
		$this->assertEquals(self::class, AnnotationParser::classnameOf(self::class));
		$this->assertEquals(self::class, AnnotationParserTest::class);

		$types = implode('|', AnnotationParser::NATIVE_TYPES);
		$this->assertEquals(AnnotationParser::NATIVE_TYPES, AnnotationParser::getMultiTypes($types));
		$this->assertEquals(['NULL'], AnnotationParser::getMultiTypes('NULL'));

		$this->assertEquals(AnnotationParser::NATIVE_TYPES[0], AnnotationParser::getFirstMultiType($types, false));
		$this->assertTrue(AnnotationParser::isNativeType(AnnotationParser::getFirstMultiType($types, false)));

		$types .= '|'.self::class;
		$this->assertEquals(self::class, AnnotationParser::getFirstMultiType($types));
		$this->assertFalse(AnnotationParser::isNativeType(AnnotationParser::getFirstMultiType($types)));

		$this->assertTrue(AnnotationParser::hasMultiTypesNull($types));
		$types = AnnotationParser::getMultiTypes($types);
		if (($key = array_search('null', $types)) !== false) unset($types[$key]);
		if (($key = array_search('NULL', $types)) !== false) unset($types[$key]);
		$types = implode('|', $types);
		$this->assertFalse(AnnotationParser::hasMultiTypesNull($types));
	}
}

