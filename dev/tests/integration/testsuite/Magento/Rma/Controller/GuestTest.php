<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Rma\Controller;

class GuestTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @param string $uri
     * @param string $content
     * @magentoConfigFixture current_store sales/magento_rma/enabled 1
     * @magentoDataFixture Magento/Rma/_files/rma.php
     * @dataProvider isResponseContainDataProvider
     */
    public function testIsResponseContain($uri, $content)
    {
        /** @var $rma \Magento\Rma\Model\Rma */
        $rma = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Rma\Model\Rma');
        $rma->load(1, 'increment_id');

        $this->getRequest()->setParam('entity_id', $rma->getEntityId());
        $this->getRequest()->setPost('oar_type', 'email');
        $this->getRequest()->setPost('oar_order_id', $rma->getOrder()->getIncrementId());
        $billingAddress = $rma->getOrder()->getBillingAddress();
        if ($billingAddress !== false) {
            $this->getRequest()->setPost('oar_billing_lastname', $billingAddress->getLastname());
            $this->getRequest()->setPost('oar_email', $billingAddress->getEmail());
        }
        $this->getRequest()->setPost('oar_zip', '');

        $this->dispatch($uri);
        $this->assertContains($content, $this->getResponse()->getBody());
    }

    public function isResponseContainDataProvider()
    {
        return array(
            array('rma/guest/addlabel', 'class="col carrier">CarrierTitle</td>'),
            array('rma/guest/dellabel', 'class="col carrier">CarrierTitle</td>')
        );
    }
}
