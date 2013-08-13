<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_Source_DesignTest extends PHPUnit_Framework_TestCase
{
    public function testGetAllOptions()
    {
        /** @var $model Mage_Core_Model_Design_Source_Design */
        $model = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Core_Model_Design_Source_Design');

        /** @var $expectedCollection Mage_Core_Model_Theme_Collection */
        $expectedCollection = Mage::getModel('Mage_Core_Model_Resource_Theme_Collection');
        $expectedCollection->addFilter('area', 'frontend');

        $expectedItemsCount = count($expectedCollection);

        $labelsCollection = $model->getAllOptions(false);
        $this->assertEquals($expectedItemsCount, count($labelsCollection));

        $labelsCollection = $model->getAllOptions(true);
        $this->assertEquals(++$expectedItemsCount, count($labelsCollection));
    }
}
