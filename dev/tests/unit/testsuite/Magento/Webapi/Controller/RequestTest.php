<?php
/**
 * \Magento\Webapi\Controller\Request
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Controller;


use Magento\TestFramework\Helper\ObjectManager;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Webapi\Controller\Request
     */
    private $request;

    /**
     * @var \Magento\Framework\Stdlib\CookieManager
     */
    private $cookieManager;

    public function setUp()
    {

        $objectManager = new ObjectManager($this);
        $this->cookieManager = $this->getMock('\Magento\Framework\Stdlib\CookieManager');

        $this->request = $objectManager->getObject(
            '\Magento\Webapi\Controller\Request',
            ['cookieManager' => $this->cookieManager]
        );
    }

    public function testGetCookie()
    {
        $key = "cookieName";
        $default = "defaultValue";

        $this->cookieManager
            ->expects($this->once())
            ->method('getCookie')
            ->with($key, $default);

        $this->request->getCookie($key, $default);
    }
} 