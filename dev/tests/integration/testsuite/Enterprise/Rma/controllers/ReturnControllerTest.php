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

class Enterprise_Rma_ReturnControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    public function setUp()
    {
        parent::setUp();
        $this->_customerSession = new Mage_Customer_Model_Session;
        $this->_customerSession->login('customer@example.com', 'password');
    }

    protected function tearDown()
    {
        $this->_customerSession->logout();
    }

    /**
     * @magentoConfigFixture current_store sales/enterprise_rma/enabled 1
     * @magentoDataFixture Enterprise/Rma/_files/rma.php
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @dataProvider isResponseContainDataProvider
     */
    public function testIsResponseContain($uri, $content)
    {
        $rma = new Enterprise_Rma_Model_Rma();
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
