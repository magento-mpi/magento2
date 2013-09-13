<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_System_Store_EditTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->unregister('store_type');
        $objectManager->get('Magento_Core_Model_Registry')->unregister('store_data');
        $objectManager->get('Magento_Core_Model_Registry')->unregister('store_action');
    }

    /**
     * @param $registryData
     */
    protected function _initStoreTypesInRegistry($registryData)
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        foreach ($registryData as $key => $value) {
            $objectManager->get('Magento_Core_Model_Registry')->register($key, $value);
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @param $registryData
     * @param $expected
     * @dataProvider getStoreTypesForLayout
     */
    public function testStoreTypeFormCreated($registryData, $expected)
    {
        $this->_initStoreTypesInRegistry($registryData);

        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_System_Store_Edit */
        $block = $layout->createBlock('Magento_Adminhtml_Block_System_Store_Edit', 'block');
        $block->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML);

        $this->assertInstanceOf($expected, $block->getChildBlock('form'));
    }

    /**
     * @return array
     */
    public function getStoreTypesForLayout()
    {
        return array(
            array(
                array('store_type' => 'website', 'store_data' => Mage::getModel('Magento_Core_Model_Website')),
                'Magento_Adminhtml_Block_System_Store_Edit_Form_Website'
            ),
            array(
                array('store_type' => 'group', 'store_data' => Mage::getModel('Magento_Core_Model_Store_Group')),
                'Magento_Adminhtml_Block_System_Store_Edit_Form_Group'
            ),
            array(
                array('store_type' => 'store', 'store_data' => Mage::getModel('Magento_Core_Model_Store')),
                'Magento_Adminhtml_Block_System_Store_Edit_Form_Store'
            )
        );
    }
    /**
     * @magentoAppIsolation enabled
     * @param $registryData
     * @param $expected
     * @dataProvider getStoreDataForBlock
     */
    public function testGetHeaderText($registryData, $expected)
    {
        $this->_initStoreTypesInRegistry($registryData);

        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_System_Store_Edit */
        $block = $layout->createBlock('Magento_Adminhtml_Block_System_Store_Edit', 'block');
        $block->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML);

        $this->assertEquals($expected, $block->getHeaderText());
    }

    /**
     * @return array
     */
    public function getStoreDataForBlock()
    {
        return array(
            array(
                array(
                    'store_type' => 'website',
                    'store_data' => Mage::getModel('Magento_Core_Model_Website'),
                    'store_action' => 'add'
                ),
                'New Web Site'
            ),
            array(
                array(
                    'store_type' => 'website',
                    'store_data' => Mage::getModel('Magento_Core_Model_Website'),
                    'store_action' => 'edit'
                ),
                'Edit Web Site'
            ),
            array(
                array(
                    'store_type' => 'group',
                    'store_data' => Mage::getModel('Magento_Core_Model_Store_Group'),
                    'store_action' => 'add'
                ),
                'New Store'
            ),
            array(
                array(
                    'store_type' => 'group',
                    'store_data' => Mage::getModel('Magento_Core_Model_Store_Group'),
                    'store_action' => 'edit'
                ),
                'Edit Store'
            ),
            array(
                array(
                    'store_type' => 'store',
                    'store_data' => Mage::getModel('Magento_Core_Model_Store'),
                    'store_action' => 'add'
                ),
                'New Store View'
            ),
            array(
                array(
                    'store_type' => 'store',
                    'store_data' => Mage::getModel('Magento_Core_Model_Store'),
                    'store_action' => 'edit'
                ),
                'Edit Store View'
            )
        );
    }
}
