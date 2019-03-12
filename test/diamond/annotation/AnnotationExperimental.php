<?php

namespace test\diamond\annotation;

use diamond\annotation\Annotation;
use diamond\annotation\AnnotationException;
use diamond\annotation\AnnotationProperty;
use DateTime;

/**
 * Experimental Annotation
 *
 * That class has as porpose be used on test cases of annotations use.
 * Implements all possible annotations parsed by the library, native and customs.
 *
 * @see Annotation
 * @see AnnotationParser::parseClass() veja que método incrível!
 *
 * @link https://libs.diverproject.org/php-annotation
 * @link www.invalid.link.annotation
 *
 * @author Andrew
 * @author Andrew Mello
 */
class AnnotationExperimental
{
	/**
	 * @var string const string property that haven't any annotation.
	 */
	const constantClassScope = 0;
	/**
	 * @var AnnotationProperty object property of type AnnotationProperty.
	 * @CustomAnnotation(name=experimental,type=example)
	 * @ExportAs int export as int value.
	 */
	private $privateObjectScope;
	/**
	 * @var AnnotationProperty|NULL object property of type AnnotationProperty nullable.
	 */
	private $null;
	/**
	 * @var int a native property.
	 */
	private $native;

	/**
	 * Privated Method without annotations.
	 */
	private function privateMethod()
	{

	}

	/**
	 * Protected method receiving parameters and return something.
	 * And a second line to increase amount of lines.
	 * @param int $number parameter of int type.
	 * @param float $decimal parameter of float type.
	 * @param bool $enabled parameter of boolean type.
	 * @return \diamond\annotation\AnnotationException|\DateTime return a exception created.
	 */
	protected function protectedMethod($number, $decimal, $enabled): AnnotationException
	{
		return new AnnotationException();
	}

	/**
	 * Public method for annother annotations not used on <code>privateMethod()</code> and <code>protectedMethod()</code>.
	 * @CustomAnnotation({"name":"Andrew Mello","age":23})
	 * @throws AnnotationException exception that can be throws but in this case always.
	 * @see \diamond\annotation\Annotation::parseClass() see static method of Annotation class.
	 * @link https://libs.diverproject.org/php-annotation
	 */
	public function publicMethod()
	{
		Annotation::parseClass(null);
		throw new AnnotationException();
	}
}
