<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Theme_LabelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Theme_Label
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Theme_Label');
    }

    /**
     * @covers Magento_Core_Model_Theme_Label::getLabelsCollection
     */
    public function testGetLabelsCollection()
    {
        /** @var $expectedCollection Magento_Core_Model_Resource_Theme_Collection */
        $expectedCollection = Mage::getModel('Magento_Core_Model_Resource_Theme_Collection');
        $expectedCollection->addAreaFilter(Magento_Core_Model_App_Area::AREA_FRONTEND)
            ->filterVisibleThemes();

        $expectedItemsCount = count($expectedCollection);

        $labelsCollection = $this->_model->getLabelsCollection();
        $this->assertEquals($expectedItemsCount, count($labelsCollection));

        $labelsCollection = $this->_model->getLabelsCollection('-- Please Select --');
        $this->assertEquals(++$expectedItemsCount, count($labelsCollection));
    }
}
