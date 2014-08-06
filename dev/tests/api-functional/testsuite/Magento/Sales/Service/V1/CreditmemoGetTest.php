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

/**
 * Class CreditmemoGetTest
 */
class CreditmemoGetTest extends WebapiAbstract
{
    /**
     * Resource path
     */
    const RESOURCE_PATH = '/V1/creditmemos';

    /**
     * Service read name
     */
    const SERVICE_READ_NAME = 'salesOrderCreditmemoGetServiceV1';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * Creditmemo id
     */
    const CREDITMEMO_INCREMENT_ID = '100000001';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Required fields are in the answer
     *
     * @var array
     */
    protected $requiredFields = [
        'entity_id',
        'store_id',
        'base_shipping_tax_amount',
        'base_discount_amount',
        'grand_total',
        'base_subtotal_incl_tax',
        'shipping_amount',
        'subtotal_incl_tax',
        'base_shipping_amount',
        'base_adjustment',
        'base_subtotal',
        'discount_amount',
        'subtotal',
        'adjustment',
        'base_grand_total',
        'base_tax_amount',
        'shipping_tax_amount',
        'tax_amount',
        'order_id',
        'state',
        'increment_id'
    ];

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * Test creditmemo get service
     *
     * @m111agentoApiDataFixture Magento/Sales/_files/creditmemo_with_list.php
     */
    public function testCreditmemoGet()
    {
        /** @var \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory */
        $creditmemoFactory = $this->objectManager->create('Magento\Sales\Model\Order\CreditmemoFactory');
        $creditmemo = $creditmemoFactory->create()->load(self::CREDITMEMO_INCREMENT_ID, 'increment_id');
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $creditmemo->getId(),
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'info'
            ]
        ];

        $expected = $this->_webApiCall($serviceInfo, ['id' => $creditmemo->getId()]);
        $actual = $creditmemo->getData();
        foreach ($this->requiredFields as $field) {
            $this->assertArrayHasKey($field, $expected);
            $this->assertEquals($expected[$field], $actual[$field]);
        }

    }
}
