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

class Magento_Core_Model_Design_Source_DesignTest extends PHPUnit_Framework_TestCase
{
    public function testGetAllOptions()
    {
        /** @var $model \Magento\Core\Model\Design\Source\Design */
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Design\Source\Design');

        /** @var $expectedCollection \Magento\Core\Model\Theme\Collection */
        $expectedCollection = Mage::getModel('\Magento\Core\Model\Resource\Theme\Collection');
        $expectedCollection->addFilter('area', 'frontend');

        $expectedItemsCount = count($expectedCollection);

        $labelsCollection = $model->getAllOptions(false);
        $this->assertEquals($expectedItemsCount, count($labelsCollection));

        $labelsCollection = $model->getAllOptions(true);
        $this->assertEquals(++$expectedItemsCount, count($labelsCollection));
    }
}
