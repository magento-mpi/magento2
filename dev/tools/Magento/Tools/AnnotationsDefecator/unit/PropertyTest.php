<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Tools\AnnotationsDefecator\unit;

require __DIR__ . '/../../../../bootstrap.php';

use Magento\Tools\AnnotationsDefecator\Line\Property;

class PropertyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $content
     * @param bool $isProperty
     * @dataProvider contentDataProvider
     */
    public function testIsFunctionLine($content, $isProperty)
    {
        $this->assertEquals($isProperty, Property::isProperty($content));
    }

    /**
     * @return array
     */
    public function contentDataProvider()
    {
        return [
            ['public $_abc', true],
            ['private $_abc1', true],
            ['protected $_abC1', true],
            ['  public $Abc1', true]
        ];
    }
}
