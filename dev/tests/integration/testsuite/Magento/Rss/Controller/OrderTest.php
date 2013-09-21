<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rss\Controller;

/**
 * @magentoAppArea adminhtml
 */
class OrderTest extends \Magento\TestFramework\TestCase\ControllerAbstract
{
    /**
     * Reuse URI for "new" action
     */
    const NEW_ORDER_URI = 'rss/order/new';

    public function testNewActionAuthorizationFailed()
    {
        $this->dispatch(self::NEW_ORDER_URI);
        $this->assertHeaderPcre('Http/1.1', '/^401 Unauthorized$/');
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testNewAction()
    {
        $this->getRequest()->setServer(array(
            'PHP_AUTH_USER' => \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            'PHP_AUTH_PW' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        ));
        $this->dispatch(self::NEW_ORDER_URI);
        $this->assertHeaderPcre('Content-Type', '/text\/xml/');
        $this->assertContains('#100000001', $this->getResponse()->getBody());
    }

    public function testNotLoggedIn()
    {
        $this->dispatch(self::NEW_ORDER_URI);
        $this->assertHeaderPcre('Http/1.1', '/^401 Unauthorized$/');
    }

    /**
     * @param string $login
     * @param string $password
     * @dataProvider invalidAccessDataProvider
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     * @covers \Magento\Rss\Controller\Order::authenticateAndAuthorizeAdmin
     */
    public function testInvalidAccess($login, $password)
    {
        $this->getRequest()->setServer(array('PHP_AUTH_USER' => $login, 'PHP_AUTH_PW' => $password));
        $this->dispatch(self::NEW_ORDER_URI);
        $this->assertHeaderPcre('Http/1.1', '/^401 Unauthorized$/');
    }

    /**
     * @return array
     */
    public function invalidAccessDataProvider()
    {
        return array(
            'no login' => array('', \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD),
            'no password' => array(\Magento\TestFramework\Bootstrap::ADMIN_NAME, ''),
            'no login and password' => array('', ''),
            'user with inappropriate ACL' => array('dummy_username', 'dummy_password1'),
        );
    }
}
