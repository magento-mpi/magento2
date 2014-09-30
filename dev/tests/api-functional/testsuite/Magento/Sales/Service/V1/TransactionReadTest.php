<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1;

use Magento\Webapi\Model\Rest\Config;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order;

class TransactionReadTest extends WebapiAbstract
{
    /**
     * Service read name
     */
    const SERVICE_READ_NAME = 'salesTransactionReadV1';

    /**
     * Resource path for REST
     */
    const RESOURCE_PATH = '/V1/transactions';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * Tests list of order transactions
     *
     * @magentoApiDataFixture Magento/Sales/_files/transactions_detailed.php
     */
    public function testTransactionGet()
    {
        /** @var Order $order */
        $order = $this->objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000006');

        /** @var Payment $payment */
        $payment = $order->getPayment();
        /** @var Transaction $transaction */
        $transaction = $payment->getTransaction('trx_auth');

        $childTransaction = $transaction->getChildTransactions()[2];

        $expectedData = $this->getPreparedTransactionData($transaction);
        $expectedData['child_transactions'][] = $this->getPreparedTransactionData($childTransaction);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $transaction->getId(),
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'get'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, ['id' => $transaction->getId()]);
        $this->assertEquals($expectedData, $result);
    }

    /**
     * Tests list of order transactions
     * @dataProvider filtersDataProvider
     */
    public function testTransactionList($filters)
    {
        /** @var Order $order */
        $order = $this->objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000006');

        /** @var Payment $payment */
        $payment = $order->getPayment();
        /** @var Transaction $transaction */
        $transaction = $payment->getTransaction('trx_auth');

        $childTransaction = $transaction->getChildTransactions()[2];

        /** @var $searchCriteriaBuilder  \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(
            'Magento\Framework\Service\V1\Data\SearchCriteriaBuilder'
        );

        $searchCriteriaBuilder->addFilter($filters);
        $searchData = $searchCriteriaBuilder->create()->__toArray();

        $requestData = ['searchCriteria' => $searchData];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'search'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertArrayHasKey('items', $result);

        $expectedData = [
            $this->getPreparedTransactionData($transaction),
            $this->getPreparedTransactionData($childTransaction)
        ];

        $this->assertEquals($expectedData, $result['items']);
    }

    /**
     * @param Transaction $transaction
     * @return array
     */
    private function getPreparedTransactionData(Transaction $transaction)
    {
        $additionalInfo = [];
        foreach ($transaction->getAdditionalInformation() as $key => $value) {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $additionalInfo[$key] = $value;
            } else {
                $additionalInfo[] = [
                    'key' => $key,
                    'value' => $value
                ];
            }
        }

        $expectedData = [
            'transaction_id' => (int)$transaction->getId(),
            'order_id' => (int)$transaction->getOrderId(),
            'payment_id' => (int)$transaction->getPaymentId(),
            'txn_id' => $transaction->getTxnId(),
            'parent_txn_id' => ($transaction->getParentTxnId() ? (string)$transaction->getParentTxnId() : ''),
            'txn_type' => $transaction->getTxnType(),
            'is_closed' => (int)$transaction->getIsClosed(),
            'additional_information' => $additionalInfo,
            'created_at' => $transaction->getCreatedAt(),
            'increment_id' => '100000006',
            'child_transactions' => [],
            'method' => 'checkmo'
        ];

        if (!is_null($transaction->getParentId())) {
            $expectedData['parent_id'] = (int)$transaction->getParentId();
        } else {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
                $expectedData['parent_id'] = null;
            }
        }

        return $expectedData;
    }

    /**
     * @return array
     */
    public function filtersDataProvider()
    {
        /** @var $filterBuilder  \Magento\Framework\Service\V1\Data\FilterBuilder */
        $filterBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\FilterBuilder'
        );

        return [
            [[$filterBuilder->setField('increment_id')->setValue('100000006')->setConditionType('eq')->create()]],
            [[$filterBuilder->setField('method')->setValue('checkmo')->setConditionType('eq')->create()]],
            [
                [
                    $filterBuilder->setField('created_at')->setValue('2020-12-12 00:00:00')
                        ->setConditionType('lteq')->create()
                ]
            ]
        ];
    }
}
