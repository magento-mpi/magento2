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
            $this->getMock('Magento\Wishlist\Controller\Index', [], [], '', false)
        );
    }

    public function testCartAction()
    {
        $request = $this->getMock('Magento\App\Request\Http', [], [], '', false);
        $response = $this->getMock('Magento\App\Response\Http', [], [], '', false);

        $wishlistItem = $this->getMock('Magento\Object',
            ['load', 'getId', 'mergeBuyRequest', 'addToCart', 'getProduct'],
            [],
            '',
            false
        );
        $wishlistItem->expects($this->once())->method('load')->will($this->returnValue($wishlistItem));
        $wishlistItem->expects($this->once())->method('getId')->will($this->returnValue(1));
        $wishlistItem->expects($this->once())->method('getProduct')->will($this->returnValue($wishlistItem));

        $objectManager = $this->getMock('Magento\ObjectManager');

        $locale = $this->getMock('Magento\Core\Model\Locale', [], [], '', false);

        $optionCollection  = $this->getMock(
            'Magento\Wishlist\Model\Resource\Item\Option\Collection',
            ['addItemFilter', 'getOptionsByItem'],
            [],
            '',
            false
        );
        $optionCollection->expects($this->once())->method('addItemFilter')->will($this->returnValue($optionCollection));

        $cart = $this->getMock('Magento\Checkout\Model\Cart', ['save', 'getQuote', 'collectTotals'], [], '', false);
        $cart->expects($this->once())->method('save')->will($this->returnValue($cart));
        $cart->expects($this->any())->method('getQuote')->will($this->returnValue($cart));

        $option = $this->getMock('Magento\Object', ['getCollection'], [], '', false);
        $option->expects($this->once())->method('getCollection')->will($this->returnValue($optionCollection));

        $product = $this->getMock('Magento\Catalog\Helper\Product', [], [], '', false);

        $escaper = $this->getMock('Magento\Excaper', ['escapeHtml'], [], '', false);

        $wishlistHelper = $this->getMock('Magento\Wishlist\Helper\Data',
            ['getShouldRedirectToCart', 'calculate', 'getCustomer'],
            [],
            '',
            false
        );

        $mapGet = [
            ['Magento\Core\Model\LocaleInterface', $locale],
            ['Magento\Checkout\Model\Cart', $cart],
            ['Magento\Catalog\Helper\Product', $product],
            ['Magento\Escaper', $escaper],
            ['Magento\Wishlist\Helper\Data', $wishlistHelper],
            ['Magento\Checkout\Helper\Cart', $wishlistHelper]
        ];

        $mapCreate = [
            ['Magento\Wishlist\Model\Item', [], $wishlistItem],
            ['Magento\Wishlist\Model\Item\Option', [], $option]
        ];

        $objectManager->expects($this->any())->method('get')->will($this->returnValueMap($mapGet));
        $objectManager->expects($this->any())->method('create')->will($this->returnValueMap($mapCreate));

        $controller = $this->_factory($request, $response, $objectManager);

        $controller->cartAction();
    }

    /**
     * Create the tested object
     *
     * @param \Magento\App\Request\Http $request
     * @param \Magento\App\Response\Http|null $response
     * @param \Magento\ObjectManager|null $objectManager
     * @return \Magento\Wishlist\Controller\Index
     */
    protected function _factory($request, $response = null, $objectManager = null)
    {
        if (!$response) {
            /** @var $response \Magento\App\ResponseInterface */
            $response = $this->getMock('Magento\App\Response\Http', [], [], '', false);
            $response->headersSentThrowsException = false;
        }
        if (!$objectManager) {
            $objectManager = new \Magento\ObjectManager\ObjectManager();
        }
        $rewriteFactory = $this->getMock('Magento\Core\Model\Url\RewriteFactory', ['create'], [], '', false);
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $varienFront = $helper->getObject('Magento\App\FrontController',
            ['rewriteFactory' => $rewriteFactory]
        );

        $arguments = [
            'request' => $request,
            'response' => $response,
            'objectManager' => $objectManager,
            'frontController' => $varienFront,
        ];
        $context = $helper->getObject('Magento\Backend\App\Action\Context', $arguments);

        $wishlistModel = $this->getMock('\Magento\Wishlist\Model\Wishlist', [], [], '', false);

        $coreRegistry = $this->getMock('\Magento\Core\Model\Registry', ['registry'], [], '', false);
        $coreRegistry->expects($this->once())->method('registry')->will($this->returnValue($wishlistModel));

        $messageManager = $this->getMock('\Magento\Message\Manager', [], [], '', false);

        return $helper->getObject('Magento\Wishlist\Controller\Index', [
            'context' => $context,
            'coreRegistry' => $coreRegistry,
            'messageManager' => $messageManager
        ]);
    }
}
