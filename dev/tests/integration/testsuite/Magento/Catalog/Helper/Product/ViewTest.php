<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Helper\Product;

/**
 * @magentoAppArea frontend
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Product\View
     */
    protected $_helper;

    /**
     * @var \Magento\Catalog\Controller\Product
     */
    protected $_controller;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\TestFramework\Helper\Bootstrap
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->objectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');
        $this->objectManager->get('Magento\Framework\App\Http\Context')
            ->setValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH, false, false);
        $this->objectManager->get('Magento\Framework\View\DesignInterface')
            ->setDefaultDesignTheme();
        $this->_helper = $this->objectManager->get('Magento\Catalog\Helper\Product\View');
        $request = $this->objectManager->get('Magento\TestFramework\Request');
        $request->setRouteName('catalog')->setControllerName('product')->setActionName('view');
        $arguments = array(
            'request' => $request,
            'response' => $this->objectManager->get('Magento\TestFramework\Response')
        );
        $context = $this->objectManager->create('Magento\Framework\App\Action\Context', $arguments);
        $this->_controller = $this->objectManager->create(
            'Magento\Catalog\Controller\Product',
            array('context' => $context)
        );

        $this->_layout = $this->objectManager->get(
            'Magento\Framework\View\LayoutInterface'
        );
    }

    /**
     * Cleanup session, contaminated by product initialization methods
     */
    protected function tearDown()
    {
        $this->objectManager->get('Magento\Catalog\Model\Session')->unsLastViewedProductId();
        $this->_controller = null;
        $this->_helper = null;
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     */
    public function testInitProductLayout()
    {
        $uniqid = uniqid();
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->objectManager->create(
            'Magento\Catalog\Model\Product'
        );
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE)->setId(99)->setUrlKey($uniqid);
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = $this->objectManager;
        $objectManager->get('Magento\Framework\Registry')->register('product', $product);

        $this->_helper->initProductLayout($product, $this->_controller);

        /** @var \Magento\Framework\View\Page\Config $pageConfig */
        $pageConfig = $this->objectManager->get('Magento\Framework\View\Page\Config');
        $bodyClass = $pageConfig->getElementAttribute(
            \Magento\Framework\View\Page\Config::ELEMENT_TYPE_BODY,
            \Magento\Framework\View\Page\Config::BODY_ATTRIBUTE_CLASS
        );
        $this->assertContains("product-{$uniqid}", $bodyClass);
        $handles = $this->_layout->getUpdate()->getHandles();
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
            $this->objectManager->get(
                'Magento\Catalog\Model\Session'
            )->getLastViewedProductId()
        );
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @magentoAppIsolation enabled
     */
    public function testPrepareAndRenderWrongController()
    {
        $objectManager = $this->objectManager;
        $controller = $objectManager->create('Magento\Catalog\Controller\Product');
        $this->_helper->prepareAndRender(10, $controller);
    }

    /**
     * @magentoAppIsolation enabled
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testPrepareAndRenderWrongProduct()
    {
        $this->_helper->prepareAndRender(999, $this->_controller);
    }
}
