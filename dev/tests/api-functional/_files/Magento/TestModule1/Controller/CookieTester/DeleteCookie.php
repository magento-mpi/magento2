<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule1\Controller\CookieTester;

/**
 * Controller to test deletion of a cookie
 */
class DeleteCookie extends \Magento\TestModule1\Controller\CookieTester
{
    /**
     *
     * @return void
     */
    public function execute()
    {
        $cookieName = $this->getRequest()->getParam('cookie_name');
        $this->getCookieManager()->deleteCookie($cookieName);
    }
}
