<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Wishlist\Controller;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    public function testControllerImplementsProductViewInterface()
    {
        $this->assertInstanceOf(
            'Magento\Catalog\Controller\Product\View\ViewInterface',
            $this->getMock('Magento\Wishlist\Controller\Index', array(), array(), '', false)
        );
    }

    public function testCartAction()
    {
        $request = $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false);
        $response = $this->getMock('Magento\Framework\App\Response\Http', array(), array(), '', false);

        $wishlistItem = $this->getMock(
            'Magento\Object',
            array('load', 'getId', 'mergeBuyRequest', 'addToCart', 'getProduct'),
            array(),
            '',
            false
        );
        $wishlistItem->expects($this->once())->method('load')->will($this->returnValue($wishlistItem));
        $wishlistItem->expects($this->once())->method('getId')->will($this->returnValue(1));
        $wishlistItem->expects($this->once())->method('getProduct')->will($this->returnValue($wishlistItem));

        $objectManager = $this->getMock('Magento\Framework\ObjectManager');

        $locale = $this->getMock('Magento\Framework\Locale\Resolver', array(), array(), '', false);

        $optionCollection = $this->getMock(
            'Magento\Wishlist\Model\Resource\Item\Option\Collection',
            array('addItemFilter', 'getOptionsByItem'),
            array(),
            '',
            false
        );
        $optionCollection->expects(
            $this->once()
        )->method(
            'addItemFilter'
        )->will(
            $this->returnValue($optionCollection)
        );

        $cart = $this->getMock(
            'Magento\Checkout\Model\Cart',
            array('save', 'getQuote', 'collectTotals'),
            array(),
            '',
            false
        );
        $cart->expects($this->once())->method('save')->will($this->returnValue($cart));
        $cart->expects($this->any())->method('getQuote')->will($this->returnValue($cart));

        $option = $this->getMock('Magento\Object', array('getCollection'), array(), '', false);
        $option->expects($this->once())->method('getCollection')->will($this->returnValue($optionCollection));

        $product = $this->getMock('Magento\Catalog\Helper\Product', array(), array(), '', false);

        $escaper = $this->getMock('Magento\Excaper', array('escapeHtml'), array(), '', false);

        $wishlistHelper = $this->getMock(
            'Magento\Wishlist\Helper\Data',
            array('getShouldRedirectToCart', 'calculate', 'getCustomer'),
            array(),
            '',
            false
        );

        $mapGet = array(
            array('Magento\Framework\Locale\ResolverInterface', $locale),
            array('Magento\Checkout\Model\Cart', $cart),
            array('Magento\Catalog\Helper\Product', $product),
            array('Magento\Escaper', $escaper),
            array('Magento\Wishlist\Helper\Data', $wishlistHelper),
            array('Magento\Checkout\Helper\Cart', $wishlistHelper)
        );

        $mapCreate = array(
            array('Magento\Wishlist\Model\Item', array(), $wishlistItem),
            array('Magento\Wishlist\Model\Item\Option', array(), $option)
        );

        $objectManager->expects($this->any())->method('get')->will($this->returnValueMap($mapGet));
        $objectManager->expects($this->any())->method('create')->will($this->returnValueMap($mapCreate));

        $controller = $this->_factory($request, $response, $objectManager);

        $controller->cartAction();
    }

    /**
     * Create the tested object
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\Response\Http|null $response
     * @param \Magento\Framework\ObjectManager|null $objectManager
     * @return \Magento\Wishlist\Controller\Index
     */
    protected function _factory($request, $response = null, $objectManager = null)
    {
        if (!$response) {
            /** @var $response \Magento\Framework\App\ResponseInterface */
            $response = $this->getMock('Magento\Framework\App\Response\Http', array(), array(), '', false);
            $response->headersSentThrowsException = false;
        }
        if (!$objectManager) {
            $config = new \Magento\Framework\ObjectManager\Config\Config();
            $factory = new \Magento\Framework\ObjectManager\Factory\Factory($config);
            $objectManager = new \Magento\Framework\ObjectManager\ObjectManager($factory, $config);
        }
        $rewriteFactory = $this->getMock(
            'Magento\UrlRewrite\Model\UrlRewriteFactory', array('create'), array(), '', false
        );
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $varienFront = $helper->getObject(
            'Magento\Framework\App\FrontController',
            array('rewriteFactory' => $rewriteFactory)
        );

        $arguments = array(
            'request' => $request,
            'response' => $response,
            'objectManager' => $objectManager,
            'frontController' => $varienFront
        );
        $context = $helper->getObject('Magento\Backend\App\Action\Context', $arguments);

        $wishlistModel = $this->getMock('\Magento\Wishlist\Model\Wishlist', array(), array(), '', false);

        $coreRegistry = $this->getMock('\Magento\Registry', array('registry'), array(), '', false);
        $coreRegistry->expects($this->once())->method('registry')->will($this->returnValue($wishlistModel));

        $messageManager = $this->getMock('\Magento\Message\Manager', array(), array(), '', false);

        return $helper->getObject(
            'Magento\Wishlist\Controller\Index',
            array('context' => $context, 'coreRegistry' => $coreRegistry, 'messageManager' => $messageManager)
        );
    }
}
