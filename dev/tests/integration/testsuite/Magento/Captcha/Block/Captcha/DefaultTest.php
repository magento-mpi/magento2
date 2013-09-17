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
         $this->_block = Mage::app()->getLayout()
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
        Mage::app()->getStore('admin')->setUrlModel(Mage::getModel('Magento_Backend_Model_Url'));
        Mage::app()->setCurrentStore(Mage::app()->getStore('admin'));

        $this->assertContains('backend/admin/refresh/refresh', $this->_block->getRefreshUrl());
    }
}
