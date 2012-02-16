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
class Enterprise_Rma_ReturnControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Enterprise_Rma_Model_Rma
     */
    protected $_rma;

    public function setUp()
    {
        parent::setUp();
        $this->_customerSession = new Mage_Customer_Model_Session;
        $this->_customerSession->login('customer@example.com', 'password');

        $this->_rma = Mage::registry('rma');
        $this->_rma->setCustomerId($this->_customerSession->getCustomerId());
        $this->_rma->save();
    }

    protected function tearDown()
    {
        $this->_customerSession->logout();
    }

    /**
     * @magentoConfigFixture current_store sales/enterprise_rma/enabled 1
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @magentoDataFixture Enterprise/Rma/_files/rma.php
     */
    public function testAddLabelActionIsContentGenerated()
    {
        $this->markTestSkipped('MAGETWO-753');
        $this->getRequest()->setParam('entity_id', $this->_rma->getEntityId());
        $this->dispatch('rma/return/addlabel');
        $this->assertContains('<td>CarrierTitle</td>', $this->getResponse()->getBody());
    }

    /**
     * @magentoConfigFixture current_store sales/enterprise_rma/enabled 1
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @magentoDataFixture Enterprise/Rma/_files/rma.php
     */
    public function testDelLabelActionIsContentGenerated()
    {
        $this->markTestSkipped('MAGETWO-753');
        $this->getRequest()->setParam('entity_id', $this->_rma->getEntityId());
        $this->dispatch('rma/return/dellabel');
        $this->assertContains('<td>CarrierTitle</td>', $this->getResponse()->getBody());
    }
}
