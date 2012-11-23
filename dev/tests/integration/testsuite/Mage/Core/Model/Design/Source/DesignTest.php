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
        $model = Mage::getObjectManager()->create('Mage_Core_Model_Design_Source_Design');

        /** @var $expectedLabelCollection Mage_Core_Model_Theme_Collection */
        $expectedLabelCollection = Mage::getModel('Mage_Core_Model_Resource_Theme_Collection');
        $expectedLabelCollection->addFilter('area', 'frontend');

        $expectedItemsCount = count($expectedLabelCollection);

        $labelsCollection = $model->getAllOptions(false);
        $this->assertEquals($expectedItemsCount, count($labelsCollection));

        $labelsCollection = $model->getAllOptions(true);
        $this->assertEquals(++$expectedItemsCount, count($labelsCollection));
    }
}
