<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Controller_ReturnTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    public function setUp()
    {
        parent::setUp();
        $this->_customerSession = Mage::getModel('Magento_Customer_Model_Session');
        $this->_customerSession->login('customer@example.com', 'password');
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
        /** @var $rma Magento_Rma_Model_Rma */
        $rma = Mage::getModel('Magento_Rma_Model_Rma');
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
            array('rma/return/addlabel', '<td>CarrierTitle</td>'),
            array('rma/return/dellabel', '<td>CarrierTitle</td>'),
        );
    }
}
