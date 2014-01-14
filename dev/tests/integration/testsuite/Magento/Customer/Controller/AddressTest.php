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

namespace Magento\Customer\Controller;

class AddressTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testIndexAction()
    {
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Session', array($logger));
        $session->login('customer@example.com', 'password');
        $this->dispatch('customer/address/index');

        $body = $this->getResponse()->getBody();
        $this->assertContains('Default Billing Address', $body);
        $this->assertContains('Default Shipping Address', $body);
        $this->assertContains('Green str, 67', $body);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testFormPostAction()
    {
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Session', array($logger));
        $session->login('customer@example.com', 'password');

        $this->getRequest()
            ->setParam('id', 1)
            ->setServer(['REQUEST_METHOD' => 'POST'])
            ->setPost([
                'form_key' => $formKey = $this->_objectManager->get('Magento\Data\Form\FormKey')->getFormKey(),
                'firstname' => 'James',
                'lastname' => 'Bond',
                'company' => 'Ebay',
                'telephone' => '1112223333',
                'fax' => '2223334444',
                'street[]' => array('1234 Monterey Rd'),
                'city' => 'San Jose',
                'region_id' => '12',
                'region' => 'California',
                'postcode' => '55555',
                'country_id' => 'US',
                'success_url' => '',
                'error_url' => ''
        ]);
        // we are overwriting the address coming from the fixture
        $this->dispatch('customer/address/formPost');

        $this->assertRedirect($this->stringContains('customer/address/index'));
        $this->assertSessionMessages(
            $this->equalTo(array('The address has been saved.')),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
    }
}
