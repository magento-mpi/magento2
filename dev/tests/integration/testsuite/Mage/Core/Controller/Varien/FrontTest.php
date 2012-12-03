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

class Mage_Core_Controller_Varien_FrontTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test request path for rewrite
     */
    const REWRITE_PATH = 'test_rewrite_path';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Controller_Varien_Front
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_model = $this->_objectManager->create('Mage_Core_Controller_Varien_Front');
    }

    public function testSetGetDefault()
    {
        $this->_model->setDefault('test', 'value');
        $this->assertEquals('value', $this->_model->getDefault('test'));

        $default = array('some_key' => 'some_value');
        $this->_model->setDefault($default);
        $this->assertEquals($default, $this->_model->getDefault());
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf('Mage_Core_Controller_Request_Http', $this->_model->getRequest());
    }

    public function testGetResponse()
    {
        if (!Magento_Test_Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test get response without sending headers');
        }
        $this->assertInstanceOf('Mage_Core_Controller_Response_Http', $this->_model->getResponse());
    }

    public function testAddGetRouter()
    {
        $router = Mage::getModel('Mage_Core_Controller_Varien_Router_Default');
        $this->assertNull($router->getFront());
        $this->_model->addRouter('test', $router);
        $this->assertSame($this->_model, $router->getFront());
        $this->assertSame($router, $this->_model->getRouter('test'));
        $this->assertEmpty($this->_model->getRouter('tt'));
    }

    public function testGetRouters()
    {
        $this->assertEmpty($this->_model->getRouters());
        $this->_model->addRouter('test', Mage::getModel('Mage_Core_Controller_Varien_Router_Default'));
        $this->assertNotEmpty($this->_model->getRouters());
    }

    public function testInit()
    {
        $this->assertEmpty($this->_model->getRouters());
        $this->_model->init();
        $this->assertNotEmpty($this->_model->getRouters());
    }

    public function testDispatch()
    {
        if (!Magento_Test_Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Cant\'t test dispatch process without sending headers');
        }
        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->_model->init();
        /* empty action */
        $this->_model->getRequest()->setRequestUri('core/index/index');
        $this->_model->dispatch();
        $this->assertEmpty($this->_model->getResponse()->getBody());
    }

    public function testGetRouterByRoute()
    {
        $this->_model->init();
        $this->assertInstanceOf('Mage_Core_Controller_Varien_Router_Base', $this->_model->getRouterByRoute(''));
        $this->assertInstanceOf(
            'Mage_Core_Controller_Varien_Router_Base',
            $this->_model->getRouterByRoute('checkout')
        );
        $this->assertInstanceOf('Mage_Core_Controller_Varien_Router_Default', $this->_model->getRouterByRoute('test'));
    }

    public function testGetRouterByFrontName()
    {
        $this->_model->init();
        $this->assertInstanceOf(
            'Mage_Core_Controller_Varien_Router_Base',
            $this->_model->getRouterByFrontName('')
        );
        $this->assertInstanceOf(
            'Mage_Core_Controller_Varien_Router_Base',
            $this->_model->getRouterByFrontName('checkout')
        );
        $this->assertInstanceOf(
            'Mage_Core_Controller_Varien_Router_Default',
            $this->_model->getRouterByFrontName('test')
        );
    }

    /**
     * Data provider for testApplyRewrites
     *
     * @return array
     */
    public function applyRewritesDataProvider()
    {
        return array(
            'url rewrite' => array(
                '$sourcePath'   => '/' . self::REWRITE_PATH,
                '$resultPath'   => null, // result path is set in test
                '$isUrlRewrite' => true,
            ),
            'configuration rewrite' => array(
                '$sourcePath'   => '/test/url/',
                '$resultPath'   => '/new_test/url/subdirectory/',
            ),
        );
    }

    /**
     * @param string $sourcePath
     * @param string $resultPath
     * @param bool $isUrlRewrite
     *
     * @dataProvider applyRewritesDataProvider
     * @magentoConfigFixture global/rewrite/test_url/from /test\/(\w*)/
     * @magentoConfigFixture global/rewrite/test_url/to   new_test/$1/subdirectory
     * @magentoDbIsolation enabled
     */
    public function testApplyRewrites($sourcePath, $resultPath, $isUrlRewrite = false)
    {
        if ($isUrlRewrite) {
            $resultPath = $this->_createUrlRewrite();
            if (!$resultPath) {
                $this->markTestIncomplete('There must be at least one CMS page');
            }
        }

        /** @var $request Mage_Core_Controller_Request_Http */
        $request = $this->_objectManager->create('Mage_Core_Controller_Request_Http');
        $request->setPathInfo($sourcePath);

        $this->_model->applyRewrites($request);
        $this->assertEquals($resultPath, $request->getPathInfo());
    }

    /**
     * Creates new URL rewrite for random CMS page and returns rewrite target path
     *
     * @return null|string
     */
    protected function _createUrlRewrite()
    {
        // get random CMS page
        /** @var $collection Mage_Cms_Model_Resource_Page_Collection */
        $collection = $this->_objectManager->create('Mage_Cms_Model_Resource_Page_Collection');
        $collection->getSelect()->limit(1);
        $items = $collection->getItems();
        if (!$items) {
            return null;
        }
        /** @var $page Mage_Cms_Model_Page */
        $page = reset($items);

        // create URL rewrite
        /** @var $rewrite Mage_Core_Model_Url_Rewrite */
        $rewrite = $this->_objectManager->create('Mage_Core_Model_Url_Rewrite');
        $targetPath = 'cms/page/view/page_id/' . $page->getId();
        $rewrite->setIdPath('cms_page/' . $page->getId())
            ->setRequestPath(self::REWRITE_PATH)
            ->setTargetPath($targetPath)
            ->save();

        return $targetPath;
    }
}
