<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class InvoiceGetTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/invoice';

    const SERVICE_READ_NAME = 'salesInvoiceGetServiceV1';

    const SERVICE_VERSION = 'V1';

    const INVOICE_INCREMENT_ID = '100000001';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInvoiceGet()
    {
        $expectedInvoiceData = [
            'grand_total' => '100.0000',
            'subtotal' => '100.0000',
            'increment_id' => self::INVOICE_INCREMENT_ID
        ];
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $this->objectManager->create('Magento\Sales\Model\Order\Invoice');
        $invoice->loadByIncrementId(self::INVOICE_INCREMENT_ID);
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $invoice->getId(),
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'invoke'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, ['id' => $invoice->getId()]);
        foreach ($expectedInvoiceData as $field => $value) {
            $this->assertArrayHasKey($field, $result);
            $this->assertEquals($value, $result[$field]);
        }
    }
}
