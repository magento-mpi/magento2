<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_Rma
 */
class Enterprise_Rma_GuestControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Enterprise_Rma_Model_Rma
     */
    protected $_rma;

    public function setUp()
    {
        parent::setUp();
        $this->_rma = Mage::registry('rma');
    }

    /**
     * @magentoConfigFixture current_store sales/enterprise_rma/enabled 1
     * @magentoDataFixture Enterprise/Rma/_files/rma.php
     */
    public function testAddLabelActionIsContentGenerated()
    {
        $rma = $this->_rma;
        $this->getRequest()->setParam('entity_id', $rma->getEntityId());
        $this->getRequest()->setPost('oar_type', 'email');
        $this->getRequest()->setPost('oar_order_id', $rma->getOrder()->getIncrementId());
        $this->getRequest()->setPost('oar_billing_lastname', $rma->getOrder()->getBillingAddress()->getLastname());
        $this->getRequest()->setPost('oar_email', $rma->getOrder()->getBillingAddress()->getEmail());
        $this->getRequest()->setPost('oar_zip', '');

        $this->dispatch('rma/guest/addlabel');
        $this->assertContains('<td>CarrierTitle</td>', $this->getResponse()->getBody());
    }

    /**
     * @magentoConfigFixture current_store sales/enterprise_rma/enabled 1
     * @magentoDataFixture Enterprise/Rma/_files/rma.php
     */
    public function testDelLabelActionIsContentGenerated()
    {
        $rma = $this->_rma;
        $this->getRequest()->setParam('entity_id', $rma->getEntityId());
        $this->getRequest()->setPost('oar_type', 'email');
        $this->getRequest()->setPost('oar_order_id', $rma->getOrder()->getIncrementId());
        $this->getRequest()->setPost('oar_billing_lastname', $rma->getOrder()->getBillingAddress()->getLastname());
        $this->getRequest()->setPost('oar_email', $rma->getOrder()->getBillingAddress()->getEmail());
        $this->getRequest()->setPost('oar_zip', '');

        $this->dispatch('rma/guest/dellabel');
        $this->assertContains('<td>CarrierTitle</td>', $this->getResponse()->getBody());
    }
}
