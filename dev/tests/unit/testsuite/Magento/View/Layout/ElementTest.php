<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\View\Layout\Element
 */
namespace Magento\View\Layout;

class ElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider elementNameDataProvider
     */
    public function testGetElementName($xml, $name)
    {
        $model = new \Magento\View\Layout\Element($xml);
        $this->assertEquals($name, $model->getElementName());
    }

    public function elementNameDataProvider()
    {
        return array(
            array('<block name="name" />', 'name'),
            array('<container name="name" />', 'name'),
            array('<referenceBlock name="name" />', 'name'),
            array('<invalid name="name" />', false),
            array('<block />', ''),
        );
    }

    /**
     * @dataProvider cacheableTypeDataProvider
     */
    public function testIsCacheableType($xml, $expected)
    {
        $model = new \Magento\View\Layout\Element($xml);
        $this->assertEquals($expected, $model->isCacheableType());
    }

    public function cacheableTypeDataProvider()
    {
        return array(
            array('<containter />', false),
            array('<block />', true),
            array('<renderer />', true),
            array('<widget />', true),
            array('<data />', false),
            array('<action />', false),
        );
    }

    public function cacheableDataProvider()
    {
        return array(
            array('<containter name="name" />', false),
            array('<block cacheable="0" />', false),
            array('<block cacheable="1" />', true),
            array('<block name="name" />', true),
            array('<renderer cacheable="0" />', false),
            array('<renderer cacheable="1" />', true),
            array('<renderer name="name" />', true),
            array('<widget cacheable="0" />', false),
            array('<widget cacheable="1" />', true),
            array('<widget name="name" />', true)
        );
    }

    /**
     * @dataProvider cacheableDataProvider
     */
    public function testIsCacheable($xml, $expected)
    {
        $model = new \Magento\View\Layout\Element($xml);
        $this->assertEquals($expected, $model->isCacheable());
    }
}
