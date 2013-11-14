<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Controller;

class CartTest extends \PHPUnit_Framework_TestCase
{
    public function testControllerImplementsProductViewInterface()
    {
        $this->assertInstanceOf(
            'Magento\Catalog\Controller\Product\View\ViewInterface',
            $this->getMock('Magento\Checkout\Controller\Cart', array(), array(), '', false)
        );
    }

    public function testGoBack()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $responseMock->headersSentThrowsException = false;
        $responseMock->expects($this->once())
            ->method('setRedirect')
            ->with('http://some-url/index.php/checkout/cart/')
            ->will($this->returnSelf());

        $requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $requestMock->expects($this->any())->method('getActionName')->will($this->returnValue('add'));
        $requestMock->expects($this->at(0))
            ->method('getParam')->with('return_url')->will($this->returnValue('http://malicious.com/'));
        $requestMock->expects($this->any())
            ->method('getParam')->will($this->returnValue(null));
        $redirect = $this->getMock('Magento\App\Request\Redirect', array(), array(), '', false);
        $redirect->expects($this->any())->method('getRefererUrl')->will($this->returnValue('http://some-url/index.php/product.html'));

        $checkoutSessionMock = $this->getMock('Magento\Checkout\Model\Session',
            array('setContinueShoppingUrl'), array(), '', false);
        $checkoutSessionMock->expects($this->once())
            ->method('setContinueShoppingUrl')
            ->with('http://some-url/index.php/product.html')
            ->will($this->returnSelf());

        $urlMock = $this->getMock('Magento\Core\Model\Url',
            array('getUrl'), array(), '', false);
        $urlMock->expects($this->once())
            ->method('getUrl')
            ->with('checkout/cart')
            ->will($this->returnValue('http://some-url/index.php/checkout/cart/'));

        $storeMock = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->any())->method('getBaseUrl')->will($this->returnValue('http://some-url/'));

        $configMock = $this->getMock('Magento\Core\Model\Store\Config', array('getConfig'), array(), '', false);
        $configMock->expects($this->once())
            ->method('getConfig')
            ->with('checkout/cart/redirect_to_cart')
            ->will($this->returnValue('1'));


        $arguments = array(
            'response' => $responseMock,
            'request' => $requestMock,
            'checkoutSession' => $checkoutSessionMock,
            'storeConfig' => $configMock,
            'redirect' => $redirect,
            'url' => $urlMock
        );

        $controller = $helper->getObject('Magento\Checkout\Controller\Cart', $arguments);

        $reflectionObject = new \ReflectionObject($controller);
        $reflectionMethod = $reflectionObject->getMethod('_goBack');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($controller);
    }
}
