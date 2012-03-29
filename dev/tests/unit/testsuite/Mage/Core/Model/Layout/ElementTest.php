<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Core
 */
/**
 * Test class for Mage_Core_Model_Layout_Element
 */
class Mage_Core_Model_Layout_ElementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider elementNameDataProvider
     */
    public function testGetElementName($xml, $name)
    {
        $model = new Mage_Core_Model_Layout_Element($xml);
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

    /**
     * @dataProvider siblingDataProvider
     */
    public function testGetSibling($xml, $expected)
    {
        $model = new Mage_Core_Model_Layout_Element($xml);
        $this->assertEquals($expected, $model->getSibling());
    }

    public function siblingDataProvider()
    {
        return array(
            array('<block name="name" before="-" />', '-'),
            array('<block name="name" before="" />', ''),
            array('<block name="name" before="first" />', 'first'),
            array('<block name="name" after="-" />', '-'),
            array('<block name="name" after="" />', ''),
            array('<block name="name" after="last" />', 'last'),
            array('<block name="name" before="first" after="last" />', 'first'),
            array('<block name="name" after="last" before="first" />', 'first'),
        );
    }
}
