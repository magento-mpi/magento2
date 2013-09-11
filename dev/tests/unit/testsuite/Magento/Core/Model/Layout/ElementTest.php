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
 * Test class for \Magento\Core\Model\Layout\Element
 */
class Magento_Core_Model_Layout_ElementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider elementNameDataProvider
     */
    public function testGetElementName($xml, $name)
    {
        $model = new \Magento\Core\Model\Layout\Element($xml);
        $this->assertEquals($name, $model->getElementName());
    }

    public function elementNameDataProvider()
    {
        return array(
            array('<block name="name" />', 'name'),
            array('<container name="name" />', 'name'),
            array('<reference name="name" />', 'name'),
            array('<invalid name="name" />', false),
            array('<block />', ''),
        );
    }
}
