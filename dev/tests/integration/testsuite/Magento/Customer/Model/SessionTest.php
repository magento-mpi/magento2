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

use Magento\TestFramework\Helper\Bootstrap;

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

    public function testLoginById()
    {
        $this->markTestSkipped('MAGETWO-18328');
        $oldSessionId = $this->_customerSession->getSessionId();
        $this->assertTrue($this->_customerSession->loginById(1)); // fixture
        $this->assertTrue($this->_customerSession->isLoggedIn());
        $newSessionId = $this->_customerSession->getSessionId();
        $this->assertNotEquals($oldSessionId, $newSessionId);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     */
    public function testLoginByIdCustomerDataLoadedCorrectly()
    {
        $fixtureCustomerId = 1;

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = Bootstrap::getObjectManager()->create('Magento\Customer\Model\Customer')->load($fixtureCustomerId);
        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = Bootstrap::getObjectManager()->get('Magento\Customer\Model\Session');
        $customerSession->loginById($fixtureCustomerId);

        $customerData = $customerSession->getCustomerData();

        $this->assertEquals($fixtureCustomerId, $customerData->getCustomerId(), "Customer data was loaded incorrectly");
    }
}
