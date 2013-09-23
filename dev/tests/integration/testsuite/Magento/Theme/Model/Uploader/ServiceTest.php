<?php
/**
 * Magento_Theme_Model_Uploader_Service
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Theme_Model_Uploader_ServiceTest extends PHPUnit_Framework_TestCase
{
    /** @var  Magento_Theme_Model_Uploader_Service */
    protected $_service;

    protected function setUp()
    {
        $this->_service = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Theme_Model_Uploader_Service');
    }

    protected function tearDown()
    {
       $this->_service = null;
    }

    public function testGetCssUploadMaxSize()
    {
        $this->assertEquals(2 * 1024*1024, $this->_service->getCssUploadMaxSize());
    }

    public function testGetJsUploadMaxSize()
    {
        $this->assertEquals(2 * 1024*1024, $this->_service->getJsUploadMaxSize());
    }

    public function testInjection()
    {
        $this->_service = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Theme_Model_Uploader_Service',
                array('uploadLimits' => array(
                    'js' => '1M',
                    'css' => '1M'
                ))
            );
        $this->assertEquals(1 * 1024*1024, $this->_service->getCssUploadMaxSize());
        $this->assertEquals(1 * 1024*1024, $this->_service->getJsUploadMaxSize());
    }
}