<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class InvoiceCommentsListTest
 */
class InvoiceCommentsListTest extends WebapiAbstract
{
    const SERVICE_NAME = 'salesInvoiceCommentsListV1';

    const SERVICE_VERSION = 'V1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInvoiceCommentsList()
    {
        $comment = 'Test comment';
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $objectManager->get('Magento\Sales\Model\Order\Invoice')->loadByIncrementId('100000001');
        $invoice->addComment($comment);
        $invoice->save();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/invoice/' . $invoice->getId() . '/comments',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'invoke'
            ]
        ];
        $requestData = ['id' => $invoice->getId()];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        foreach ($result['items'] as $item) {
            /** @var \Magento\Sales\Model\Order\Invoice\Comment $invoiceHistoryStatus */
            $invoiceHistoryStatus = $objectManager->get('Magento\Sales\Model\Order\Invoice\Comment')
                ->load($item['entity_id']);
            $this->assertEquals($invoiceHistoryStatus->getComment(), $item['comment']);
        }
    }
}
