<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Admin/_files/user.php
 * @group module:Enterprise_Checkout
 */
class Enterprise_Checkout_Adminhtml_CheckoutControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected function setUp()
    {
        parent::setUp();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();
        $this->_session = new Mage_Admin_Model_Session();
        $this->_session->login('user', 'password');
    }

    public function testLoadBlockAction()
    {
        $this->getRequest()->setParam('block', ',');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('admin/checkout/loadBlock');
        $this->assertStringMatchesFormat('{"message":"%ACustomer not found%A"}', $this->getResponse()->getBody());
    }
}
