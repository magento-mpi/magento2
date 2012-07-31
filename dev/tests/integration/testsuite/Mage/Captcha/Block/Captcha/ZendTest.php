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
class Mage_Captcha_Block_Captcha_ZendTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Captcha_Block_Captcha_Zend
     */
    protected $_block;

    public function setUp()
    {
        $this->_block = Mage::app()->getLayout()
            ->createBlock('Mage_Captcha_Block_Captcha_Zend');
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
        Mage::app()->getStore('admin')->setUrlClassName('Mage_Backend_Model_Url');
        Mage::app()->setCurrentStore(Mage::app()->getStore('admin'));

        $this->assertContains('backend/admin/refresh/refresh', $this->_block->getRefreshUrl());
    }
}
