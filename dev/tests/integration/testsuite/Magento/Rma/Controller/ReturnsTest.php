<?php
/**
 * {license_notice}
 *
 * @category Magento
 * @package Magento_Rma
 * @subpackage integration_tests
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Rma\Controller;

class ReturnsTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    protected function setUp()
    {
        parent::setUp();
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $this->_customerSession = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Session', array($logger));
        $service = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerAccountService');
        $customer = $service->authenticate('customer@example.com', 'password');
        $this->_customerSession->setCustomerDtoAsLoggedIn($customer);
    }

    protected function tearDown()
    {
        $this->_customerSession->logout();
        $this->_customerSession = null;
        parent::tearDown();
    }

    /**
     * @magentoConfigFixture current_store sales/magento_rma/enabled 1
     * @magentoDataFixture Magento/Rma/_files/rma.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @dataProvider isResponseContainDataProvider
     */
    public function testIsResponseContain($uri, $content)
    {
        /** @var $rma \Magento\Rma\Model\Rma */
        $rma = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Rma\Model\Rma');
        $rma->load(1, 'increment_id');
        $rma->setCustomerId($this->_customerSession->getCustomerId());
        $rma->save();

        $this->getRequest()->setParam('entity_id', $rma->getEntityId());

        $this->dispatch($uri);
        $this->assertContains($content, $this->getResponse()->getBody());
    }

    public function isResponseContainDataProvider()
    {
        return array(
            array('rma/returns/addlabel', '<td class="col carrier">CarrierTitle</td>'),
            array('rma/returns/dellabel', '<td class="col carrier">CarrierTitle</td>'),
        );
    }
}
