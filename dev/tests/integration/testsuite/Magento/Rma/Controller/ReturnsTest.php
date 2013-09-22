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

class Magento_Rma_Controller_ReturnsTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    protected function setUp()
    {
        parent::setUp();
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);
        $this->_customerSession = Mage::getModel('Magento_Customer_Model_Session', array($logger));
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
        /** @var $rma \Magento\Rma\Model\Rma */
        $rma = Mage::getModel('Magento\Rma\Model\Rma');
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
            array('rma/returns/addlabel', '<td>CarrierTitle</td>'),
            array('rma/returns/dellabel', '<td>CarrierTitle</td>'),
        );
    }
}
