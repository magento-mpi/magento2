<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract,
    Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class InvoiceEmailTest
 *
 * @package Magento\Sales\Service\V1
 */
class InvoiceEmailTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';

    const SERVICE_NAME = 'salesInvoiceWriteV1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInvoiceEmail()
    {
        $objectManager= \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $invoiceCollection = $objectManager->get('Magento\Sales\Model\Resource\Order\Invoice\Collection');
        $invoice = $invoiceCollection->getFirstItem();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/invoice/' . $invoice->getId() . '/email',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'email'
            ]
        ];
        $requestData = ['id' => $invoice->getId()];
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
    }
}