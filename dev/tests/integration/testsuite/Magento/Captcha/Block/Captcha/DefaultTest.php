<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Captcha_Block_Captcha_DefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Captcha_Block_Captcha_Default
     */
    protected $_block;

    protected function setUp()
    {
         $this->_block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Captcha_Block_Captcha_Default');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetRefreshUrlWhenFrontendStore()
    {
        $this->assertContains('captcha/refresh', $this->_block->getRefreshUrl());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetRefreshUrlWhenIsAdminStore()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore('admin')->setUrlModel(
                Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                    ->create('Magento_Backend_Model_Url')
            );
        Mage::app()->setCurrentStore(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_StoreManagerInterface')->getStore('admin')
        );

        $this->assertContains('backend/admin/refresh/refresh', $this->_block->getRefreshUrl());
    }
}
