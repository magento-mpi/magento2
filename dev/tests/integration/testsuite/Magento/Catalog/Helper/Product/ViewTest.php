<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Dir')->getDir()
    . '/app/code/Magento/Catalog/Controller/Product.php';

class Magento_Catalog_Helper_Product_ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Helper_Product_View
     */
    protected $_helper;

    /**
     * @var Magento_Catalog_Controller_Product
     */
    protected $_controller;

    protected function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_View_DesignInterface')
            ->setDefaultDesignTheme();
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Catalog_Helper_Product_View');
        $request = $objectManager->create('Magento_TestFramework_Request');
        $request->setRouteName('catalog')
            ->setControllerName('product')
            ->setActionName('view');
        $arguments = array(
            'request' => $request,
            'response' => Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->get('Magento_TestFramework_Response'),
        );
        $context = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Controller_Varien_Action_Context', $arguments);
        $this->_controller = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create(
            'Magento_Catalog_Controller_Product',
            array(
                'context'  => $context,
            )
        );
    }

    /**
     * Cleanup session, contaminated by product initialization methods
     */
    protected function tearDown()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Catalog_Model_Session')
            ->unsLastViewedProductId();
        $this->_controller = null;
        $this->_helper = null;
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testInitProductLayout()
    {
        $uniqid = uniqid();
        /** @var $product Magento_Catalog_Model_Product */
        $product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
        $product->setTypeId(Magento_Catalog_Model_Product_Type::DEFAULT_TYPE)->setId(99)->setUrlKey($uniqid);
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('product', $product);

        $this->_helper->initProductLayout($product, $this->_controller);
        $rootBlock = $this->_controller->getLayout()->getBlock('root');
        $this->assertInstanceOf('Magento_Page_Block_Html', $rootBlock);
        $this->assertContains("product-{$uniqid}", $rootBlock->getBodyClass());
        $handles = $this->_controller->getLayout()->getUpdate()->getHandles();
        $this->assertContains('catalog_product_view_type_simple', $handles);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     */
    public function testPrepareAndRender()
    {
        $this->_helper->prepareAndRender(10, $this->_controller);
        $this->assertNotEmpty($this->_controller->getResponse()->getBody());
        $this->assertEquals(
            10,
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Catalog_Model_Session')
                ->getLastViewedProductId()
        );
    }

    /**
     * @expectedException Magento_Core_Exception
     * @magentoAppIsolation enabled
     */
    public function testPrepareAndRenderWrongController()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $controller = $objectManager->create(
            'Magento_Core_Controller_Front_Action',
            array(
                'request'  => $objectManager->get('Magento_TestFramework_Request'),
                'response' => $objectManager->get('Magento_TestFramework_Response'),
            )
        );
        $this->_helper->prepareAndRender(10, $controller);
    }

    /**
     * @magentoAppIsolation enabled
     * @expectedException Magento_Core_Exception
     */
    public function testPrepareAndRenderWrongProduct()
    {
        $this->_helper->prepareAndRender(999, $this->_controller);
    }

    /**
     * Test for _getSessionMessageModels
     *
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     * @magentoAppIsolation enabled
     * @covers Magento_Catalog_Helper_Product_View::_getSessionMessageModels
     * @magentoAppArea frontend
     */
    public function testGetSessionMessageModels()
    {
        $expectedMessages = array(
            'Magento_Catalog_Model_Session'  => 'catalog message',
            'Magento_Checkout_Model_Session' => 'checkout message',
        );

        // add messages
        foreach ($expectedMessages as $sessionModel => $messageText) {
            /** @var $session Magento_Core_Model_Session_Abstract */
            $session = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get($sessionModel);
            $session->addNotice($messageText);
        }

        // _getSessionMessageModels invokes inside prepareAndRender
        $this->_helper->prepareAndRender(10, $this->_controller);

        // assert messages
        $actualMessages = $this->_controller->getLayout()
            ->getMessagesBlock()
            ->getMessages();
        $this->assertSameSize($expectedMessages, $actualMessages);

        sort($expectedMessages);

        /** @var $message Magento_Core_Model_Message_Notice */
        foreach ($actualMessages as $key => $message) {
            $actualMessages[$key] = $message->getText();
        }
        sort($actualMessages);

        $this->assertEquals($expectedMessages, $actualMessages);
    }
}
