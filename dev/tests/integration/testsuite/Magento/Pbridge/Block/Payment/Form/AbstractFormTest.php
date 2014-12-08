<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Block\Payment\Form;

class AbstractFormTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSourceUrl()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $encryption = $this->getMock('Magento\Pci\Model\Encryption', [], [], '', false);
        $encryption->expects($this->any())
            ->method('encrypt')
            ->will($this->returnArgument(0));
        $encryptionFactory = $this->getMock('Magento\Pbridge\Model\EncryptionFactory', [], [], '', false);
        $encryptionFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($encryption));
        $pbridgeData = $objectManager->create('Magento\Pbridge\Helper\Data', [
            'encryptionFactory' => $encryptionFactory
        ]);

        $paymentMethod = $this->getMock('Magento\Pbridge\Model\Payment\Method', [], [], '', false);
        $paymentMethod->expects($this->any())
            ->method('getPbridgeMethodInstance')
            ->will($this->returnValue(new \Magento\Framework\Object()));
        /** @var \Magento\Pbridge\Block\Payment\Form\AbstractForm $block */
        $block = $objectManager->create('Magento\Pbridge\Block\Payment\Form\ExtendsAbstractForm', [
            'pbridgeData' => $pbridgeData,
            'data' => ['method' => $paymentMethod]
        ]);

        $sourceUrl = $block->getSourceUrl();

        $sourceUrl = parse_url($sourceUrl);
        $this->assertArrayHasKey('query', $sourceUrl, 'Source URL has no query part.');

        parse_str($sourceUrl['query'], $data);
        $this->assertArrayHasKey('data', $data, 'Data query param expected.');

        $data = json_decode($data['data'], true);
        $requiredParams = [
            'redirect_url',
            'request_gateway_code',
            'magento_payment_action',
            'css_url',
            'customer_id',
            'customer_name',
            'customer_email',
            'billing',
            'shipping',
            'cart',
        ];

        foreach ($requiredParams as $param) {
            $this->assertArrayHasKey($param, $data, 'Required source URL parameter is missing: ' . $param);
        }
    }
}
