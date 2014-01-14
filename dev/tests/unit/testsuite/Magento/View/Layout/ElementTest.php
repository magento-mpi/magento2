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

    public function cacheableDataProvider()
    {
        return array(
            array('<containter name="name" />', true),
            array('<block name="name" cacheable="false" />', false),
            array('<block name ="bl1"><block name="bl2" /></block>', true),
            array('<block name ="bl1"><block name="bl2" cacheable="false"/></block>', false),
            array('<block name="name" />', true),
            array('<renderer cacheable="false" />', false),
            array('<renderer name="name" />', true),
            array('<widget cacheable="false" />', false),
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
