<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order;

/**
 * @magentoAppArea adminhtml
 */
class CreateTest extends \Magento\Backend\Utility\Controller
{
    public function testLoadBlockAction()
    {
        $this->getRequest()->setParam('block', ',');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/sales/order_create/loadBlock');
        $this->assertEquals('{"message":""}', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testLoadBlockActionData()
    {
        $this->_objectManager->get(
            'Magento\Sales\Model\AdminOrder\Create'
        )->addProducts(
            array(1 => array('qty' => 1))
        );
        $this->getRequest()->setParam('block', 'data');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/sales/order_create/loadBlock');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<div id=\"sales_order_create_search_grid\">', $html);
        $this->assertContains('<div id=\"order-billing_method_form\">', $html);
        $this->assertContains('id=\"shipping-method-overlay\"', $html);
        $this->assertContains('id=\"coupons:code\"', $html);
    }

    /**
     * @dataProvider loadBlockActionsDataProvider
     */
    public function testLoadBlockActions($block, $expected)
    {
        $this->getRequest()->setParam('block', $block);
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/sales/order_create/loadBlock');
        $html = $this->getResponse()->getBody();
        $this->assertContains($expected, $html);
    }

    public function loadBlockActionsDataProvider()
    {
        return array(
            'shipping_method' => array('shipping_method', 'id=\"shipping-method-overlay\"'),
            'billing_method' => array('billing_method', '<div id=\"order-billing_method_form\">'),
            'newsletter' => array('newsletter', 'name=\"newsletter:subscribe\"'),
            'search' => array('search', '<div id=\"sales_order_create_search_grid\">'),
            'search_grid' => array('search', '<div id=\"sales_order_create_search_grid\">')
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testLoadBlockActionItems()
    {
        $this->_objectManager->get(
            'Magento\Sales\Model\AdminOrder\Create'
        )->addProducts(
            array(1 => array('qty' => 1))
        );
        $this->getRequest()->setParam('block', 'items');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/sales/order_create/loadBlock');
        $html = $this->getResponse()->getBody();
        $this->assertContains('id=\"coupons:code\"', $html);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoAppArea adminhtml
     */
    public function testIndexAction()
    {
        /** @var $order \Magento\Sales\Model\AdminOrder\Create */
        $order = $this->_objectManager->get('Magento\Sales\Model\AdminOrder\Create');
        $order->addProducts(array(1 => array('qty' => 1)));
        $this->dispatch('backend/sales/order_create/index');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<div id="order-customer-selector"', $html);
        $this->assertContains('<div id="sales_order_create_customer_grid">', $html);
        $this->assertContains('<div id="order-billing_method_form">', $html);
        $this->assertContains('id="shipping-method-overlay"', $html);
        $this->assertContains('<div id="sales_order_create_search_grid">', $html);
        $this->assertContains('id="coupons:code"', $html);
    }

    /**
     * @param string $actionName
     * @param boolean $reordered
     * @param string $expectedResult
     *
     * @dataProvider getAclResourceDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetAclResource($actionName, $reordered, $expectedResult)
    {
        $this->_objectManager->get('Magento\Backend\Model\Session\Quote')->setReordered($reordered);
        $orderController = $this->_objectManager->get('\Magento\Sales\Controller\Adminhtml\Order\Create');

        $this->getRequest()->setActionName($actionName);

        $method = new \ReflectionMethod('\Magento\Sales\Controller\Adminhtml\Order\Create', '_getAclResource');
        $method->setAccessible(true);
        $result = $method->invoke($orderController);
        $this->assertEquals($result, $expectedResult);
    }

    /**
     * @return array
     */
    public function getAclResourceDataProvider()
    {
        return array(
            array('index', false, 'Magento_Sales::create'),
            array('index', true, 'Magento_Sales::reorder'),
            array('save', false, 'Magento_Sales::create'),
            array('save', true, 'Magento_Sales::reorder'),
            array('reorder', false, 'Magento_Sales::reorder'),
            array('reorder', true, 'Magento_Sales::reorder'),
            array('cancel', false, 'Magento_Sales::cancel'),
            array('cancel', true, 'Magento_Sales::reorder'),
            array('', false, 'Magento_Sales::actions'),
            array('', true, 'Magento_Sales::actions')
        );
    }

    /**
     * @magentoDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     * @magentoAppArea adminhtml
     */
    public function testConfigureProductToAddAction()
    {
        $this->getRequest()->setParam('id', 1)
            ->setParam('isAjax', true);

        $this->dispatch('backend/sales/order_create/configureProductToAdd');

        $body = $this->getResponse()->getBody();

        $this->assertNotEmpty($body);
        $this->assertContains('>Quantity</label>', $body);
        $this->assertContains('>Test Configurable</label>', $body);
        $this->assertContains('"code":"test_configurable","label":"Test Configurable"', $body);
        $this->assertContains(
            '"label":"Option 1","price":"5","oldPrice":"5",'.
            '"inclTaxPrice":"5","exclTaxPrice":"5","products":[',
            $body
        );
        $this->assertContains(
            '"label":"Option 2","price":"5","oldPrice":"5",'.
            '"inclTaxPrice":"5","exclTaxPrice":"5","products":[',
            $body
        );
        $this->assertContains(
            '"basePrice":"100","oldPrice":"100","productId":"1","chooseText":"Choose an Option..."',
            $body
        );
    }

    public function testDeniedSaveAction()
    {
        $this->_objectManager->configure(
            [
                'Magento\Backend\App\Action\Context' => [
                    'arguments' => [
                        'authorization' => [
                            'instance' => 'Magento\Sales\Controller\Adminhtml\Order\AuthorizationMock'
                        ]
                    ]
                ]
            ]
        );
        \Magento\TestFramework\Helper\Bootstrap::getInstance()
            ->loadArea('adminhtml');

        $this->dispatch('backend/sales/order_create/save');
        $this->assertEquals('denied', $this->getRequest()->getActionName());
    }
}

class AuthorizationMock extends \Magento\Framework\Authorization
{
    /**
     * Check current user permission on resource and privilege
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  boolean
     */
    public function isAllowed($resource, $privilege = null)
    {
        return $resource == 'Magento_Customer::manage' ? false : parent::isAllowed($resource, $privilege);
    }
}
