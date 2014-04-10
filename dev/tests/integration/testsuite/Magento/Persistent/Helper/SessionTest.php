<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Persistent\Helper;

/**
 * @magentoDataFixture Magento/Persistent/_files/persistent.php
 * @magentoConfigFixture current_store persistent/options/customer 1
 * @magentoConfigFixture current_store persistent/options/enabled 1
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSessionHelper;

    public function setUp()
    {
        /** @var \Magento\Persistent\Model\Session $persistentSession */
        $persistentSession = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Persistent\Model\Session'
        );
        $persistentSession->loadByCustomerId(1);

        $this->_persistentSessionHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Persistent\Helper\Session'
        );

        $this->_persistentSessionHelper->setSession($persistentSession);
    }

    public function testGetCustomerDataObject()
    {
        /** @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService */
        $customerAccountService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );

        $this->assertEquals(
            $customerAccountService->getCustomer(1),
            $this->_persistentSessionHelper->getCustomerDataObject()
        );
    }
}
