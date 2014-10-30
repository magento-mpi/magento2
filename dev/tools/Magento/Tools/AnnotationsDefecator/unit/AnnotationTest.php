<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\AnnotationsDefecator\unit;

require __DIR__ . '/../../../../bootstrap.php';

use Magento\Tools\AnnotationsDefecator\Annotation;

class AnnotationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $content
     * @param bool $isAnnotation
     * @dataProvider contentDataProvider
     */
    public function testIsAnnotationWrapper($content, $isAnnotation)
    {
        $this->assertEquals($isAnnotation, Annotation::isAnnotationWrapper($content));
    }

    /**
     * @return array
     */
    public function contentDataProvider()
    {
        return [
            [Annotation::$wrappers[0], true],
            [Annotation::$wrappers[1], true],
            ['   ' . Annotation::$wrappers[0], true],
            ['   ' . Annotation::$wrappers[1], true],
            [Annotation::$wrappers[0] . ' $a ', false],
            [Annotation::$wrappers[1] . ' a ', false],
            ['   ' . Annotation::$wrappers[0] . ' a ', false],
            ['   ' . Annotation::$wrappers[1] . ' a ', false],
            [Annotation::$wrappers[0] . ' ' . Annotation::$wrappers[1], false]
        ];
    }
}
