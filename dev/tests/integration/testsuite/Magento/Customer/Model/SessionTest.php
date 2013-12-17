<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;

/**
 * @magentoDataFixture Magento/Customer/_files/customer.php
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    protected function setUp()
    {
        $this->_customerSession = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Session');
    }

    public function testLogin()
    {
        $this->markTestSkipped('MAGETWO-18328');
        $oldSessionId = $this->_customerSession->getSessionId();
        $this->assertTrue($this->_customerSession->login('customer@example.com', 'password')); // fixture
        $this->assertTrue($this->_customerSession->isLoggedIn());
        $newSessionId = $this->_customerSession->getSessionId();
        $this->assertNotEquals($oldSessionId, $newSessionId);
    }

    public function testLoginById()
    {
        $this->markTestSkipped('MAGETWO-18328');
        $oldSessionId = $this->_customerSession->getSessionId();
        $this->assertTrue($this->_customerSession->loginById(1)); // fixture
        $this->assertTrue($this->_customerSession->isLoggedIn());
        $newSessionId = $this->_customerSession->getSessionId();
        $this->assertNotEquals($oldSessionId, $newSessionId);
    }
}
