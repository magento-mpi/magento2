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

class Enterprise_Rma_Controller_GuestTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @param string $uri
     * @param string $content
     * @magentoConfigFixture current_store sales/enterprise_rma/enabled 1
     * @magentoDataFixture Enterprise/Rma/_files/rma.php
     * @dataProvider isResponseContainDataProvider
     */
    public function testIsResponseContain($uri, $content)
    {
        /** @var $rma Enterprise_Rma_Model_Rma */
        $rma = Mage::getModel('Enterprise_Rma_Model_Rma');
        $rma->load(1, 'increment_id');

        $this->getRequest()->setParam('entity_id', $rma->getEntityId());
        $this->getRequest()->setPost('oar_type', 'email');
        $this->getRequest()->setPost('oar_order_id', $rma->getOrder()->getIncrementId());
        $this->getRequest()->setPost('oar_billing_lastname', $rma->getOrder()->getBillingAddress()->getLastname());
        $this->getRequest()->setPost('oar_email', $rma->getOrder()->getBillingAddress()->getEmail());
        $this->getRequest()->setPost('oar_zip', '');

        $this->dispatch($uri);
        $this->assertContains($content, $this->getResponse()->getBody());
    }

    public function isResponseContainDataProvider()
    {
        return array(
            array('rma/guest/addlabel', '<td>CarrierTitle</td>'),
            array('rma/guest/dellabel', '<td>CarrierTitle</td>'),
        );
    }
}
