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

class Magento_Core_Model_AppTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_App
     */
    protected $_model;

    /**
     * Application instance initialized with environment
     * Is used in some tests that require initialization
     *
     * @var Magento_Core_Model_App
     */
    protected $_mageModel;

    /**
     * Callback test flag
     *
     * @var bool
     */
    protected $_errorCatchFlag = false;

    protected function setUp()
    {
        $this->_model       = Mage::getModel('Magento_Core_Model_App');
        $this->_mageModel   = Mage::app();
    }

    public function testGetCookie()
    {
        $this->assertInstanceOf('Magento_Core_Model_Cookie', $this->_model->getCookie());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testIsSingleStoreModeWhenEnabled()
    {
        $this->assertTrue($this->_mageModel->isSingleStoreMode());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store general/single_store_mode/enabled 0
     */
    public function testIsSingleStoreModeWhenDisabled()
    {
        $this->assertFalse($this->_mageModel->isSingleStoreMode());;
    }

    public function testHasSingleStore()
    {
        $this->assertTrue($this->_model->hasSingleStore());
        $this->assertTrue($this->_mageModel->hasSingleStore());
    }

    public function testSetCurrentStore()
    {
        $store = Mage::getModel('Magento_Core_Model_Store');
        $this->_model->setCurrentStore($store);
        $this->assertSame($store, $this->_model->getStore());
    }

    public function testSetErrorHandler()
    {
        $this->_model->setErrorHandler(array($this, 'errorHandler'));
        try {
            trigger_error('test', E_USER_NOTICE);
            if (!$this->_errorCatchFlag) {
                $this->fail('Error handler is not working');
            }
            restore_error_handler();
        } catch (Exception $e) {
            restore_error_handler();
            throw $e;
        }
    }

    public function errorHandler()
    {
        $this->_errorCatchFlag = true;
    }

    public function testGetArea()
    {
        $area = $this->_model->getArea('frontend');
        $this->assertInstanceOf('Magento_Core_Model_App_Area', $area);
        $this->assertSame($area, $this->_model->getArea('frontend'));
    }

    /**
     * @expectedException Magento_Core_Model_Store_Exception
     */
    public function testGetNotExistingStore()
    {
        $this->_mageModel->getStore(100);
    }

    public function testGetSafeNotExistingStore()
    {
        $this->_mageModel->getSafeStore(100);
        $this->assertEquals('noRoute', $this->_mageModel->getRequest()->getActionName());
    }

    public function testGetStores()
    {
        $this->assertNotEmpty($this->_mageModel->getStores());
        $this->assertNotContains(Magento_Core_Model_App::ADMIN_STORE_ID, array_keys($this->_mageModel->getStores()));
        $this->assertContains(Magento_Core_Model_App::ADMIN_STORE_ID, array_keys($this->_mageModel->getStores(true)));
    }

    public function testGetDefaultStoreView()
    {
        $store = $this->_mageModel->getDefaultStoreView();
        $this->assertEquals('default', $store->getCode());
    }

    public function testGetDistroLocaleCode()
    {
        $this->assertEquals(Magento_Core_Model_App::DISTRO_LOCALE_CODE, $this->_model->getDistroLocaleCode());
    }

    /**
     * @expectedException Magento_Core_Exception
     */
    public function testGetWebsiteNonExisting()
    {
        $this->assertNotEmpty($this->_mageModel->getWebsite(true)->getId());
        $this->_mageModel->getWebsite(100);
    }

    public function testGetWebsites()
    {
        $this->assertNotEmpty($this->_mageModel->getWebsites());
        $this->assertNotContains(0, array_keys($this->_mageModel->getWebsites()));
        $this->assertContains(0, array_keys($this->_mageModel->getWebsites(true)));
    }

    /**
     * @expectedException Magento_Core_Exception
     */
    public function testGetGroupNonExisting()
    {
        $this->assertNotEmpty($this->_mageModel->getGroup(true)->getId());
        $this->_mageModel->getGroup(100);
    }

    public function testGetLocale()
    {
        $locale = $this->_model->getLocale();
        $this->assertInstanceOf('Magento_Core_Model_LocaleInterface', $locale);
        $this->assertSame($locale, $this->_model->getLocale());
    }

    /**
     * @dataProvider getHelperDataProvider
     */
    public function testGetHelper($inputHelperName, $expectedClass)
    {
        $this->assertInstanceOf($expectedClass, $this->_model->getHelper($inputHelperName));
    }

    public function getHelperDataProvider()
    {
        return array(
            'class name'  => array('Magento_Core_Helper_Data', 'Magento_Core_Helper_Data'),
            'module name' => array('Magento_Core',             'Magento_Core_Helper_Data'),
        );
    }

    public function testGetBaseCurrencyCode()
    {
        $this->assertEquals('USD', $this->_model->getBaseCurrencyCode());
    }

    public function testGetFrontController()
    {
        $front = $this->_mageModel->getFrontController();
        $this->assertInstanceOf('Magento_Core_Controller_Varien_Front', $front);
        $this->assertSame($front, $this->_mageModel->getFrontController());
    }

    public function testGetCacheInstance()
    {
        $cache = $this->_mageModel->getCacheInstance();
        $this->assertInstanceOf('Magento_Core_Model_CacheInterface', $cache);
        $this->assertSame($cache, $this->_mageModel->getCacheInstance());
    }

    public function testGetCache()
    {
        $this->assertInstanceOf('Magento_Cache_FrontendInterface', $this->_mageModel->getCache());
    }

    public function testLoadSaveRemoveCache()
    {
        $this->assertEmpty($this->_mageModel->loadCache('test_id'));
        $this->_mageModel->saveCache('test_data', 'test_id');
        $this->assertEquals('test_data', $this->_mageModel->loadCache('test_id'));
        $this->_mageModel->removeCache('test_id');
        $this->assertEmpty($this->_mageModel->loadCache('test_id'));
    }

    public function testCleanCache()
    {
        $this->assertEmpty($this->_mageModel->loadCache('test_id'));
        $this->_mageModel->saveCache('test_data', 'test_id', array('test_tag'));
        $this->assertEquals('test_data', $this->_mageModel->loadCache('test_id'));
        $this->_mageModel->cleanCache(array('test_tag'));
        $this->assertEmpty($this->_mageModel->loadCache('test_id'));
    }

    public function testSetGetRequest()
    {
        $this->assertInstanceOf('Magento_Core_Controller_Request_Http', $this->_model->getRequest());
        $request = new Magento_Test_Request();
        $this->_model->setRequest($request);
        $this->assertSame($request, $this->_model->getRequest());
    }

    public function testSetGetResponse()
    {
        $this->assertInstanceOf('Magento_Core_Controller_Response_Http', $this->_model->getResponse());
        $expectedHeader = array(
            'name' => 'Content-Type',
            'value' => 'text/html; charset=UTF-8',
            'replace' => false
        );
        $this->assertContains($expectedHeader, $this->_model->getResponse()->getHeaders());
        $response = new Magento_Test_Response();
        $this->_model->setResponse($response);
        $this->assertSame($response, $this->_model->getResponse());
        $this->assertEmpty($this->_model->getResponse()->getHeaders());
    }

    /**
     * @expectedException Magento_Core_Model_Store_Exception
     */
    public function testThrowStoreException()
    {
        $this->_model->throwStoreException('test');
    }

    public function testSetGetUseSessionVar()
    {
        $this->assertFalse($this->_model->getUseSessionVar());
        $this->_model->setUseSessionVar(true);
        $this->assertTrue($this->_model->getUseSessionVar());
    }

    public function testGetAnyStoreView()
    {
        $this->assertInstanceOf('Magento_Core_Model_Store', $this->_mageModel->getAnyStoreView());
    }

    public function testSetGetUseSessionInUrl()
    {
        $this->assertTrue($this->_model->getUseSessionInUrl());
        $this->_model->setUseSessionInUrl(false);
        $this->assertFalse($this->_model->getUseSessionInUrl());
    }

    public function testGetGroups()
    {
        $groups = $this->_mageModel->getGroups();
        $this->assertInternalType('array', $groups);
        $this->assertGreaterThanOrEqual(1, count($groups));
    }
}
