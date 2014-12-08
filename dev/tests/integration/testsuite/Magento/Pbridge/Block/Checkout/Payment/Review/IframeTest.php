<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Block\Checkout\Payment\Review;

class IframeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSourceUrl()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $encryption = $this->getMock('Magento\Framework\Encryption\Encryptor', [], [], '', false);
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
        /** @var \Magento\Pbridge\Block\Checkout\Payment\Review\Iframe $block */
        $block = $objectManager->create('Magento\Pbridge\Block\Checkout\Payment\Review\Iframe', [
            'pbridgeData' => $pbridgeData,
            'data' => array('method' => $paymentMethod)
        ]);

        $sourceUrl = $block->getSourceUrl();

        $sourceUrl = parse_url($sourceUrl);
        $this->assertArrayHasKey('query', $sourceUrl, 'Source URL has no query part.');

        parse_str($sourceUrl['query'], $data);
        $this->assertArrayHasKey('data', $data, 'Data query param expected.');

        $data = json_decode($data['data'], true);
        $requiredParams = [
            'notify_url',
            'redirect_url_success',
            'redirect_url_error',
            'request_gateway_code',
            'token',
            'already_entered',
            'magento_payment_action',
            'css_url',
            'customer_id',
            'customer_name',
            'customer_email',
            'client_ip'
        ];

        foreach ($requiredParams as $param) {
            $this->assertArrayHasKey($param, $data, 'Required source URL parameter is missing: ' . $param);
        }
    }
}
