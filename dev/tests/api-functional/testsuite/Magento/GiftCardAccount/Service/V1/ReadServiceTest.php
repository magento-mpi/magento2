<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use \Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccount as GiftCardAccount;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'giftCardAccountReadServiceV1';
    const RESOURCE_PATH = '/V1/carts/';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoApiDataFixture Magento/GiftCardAccount/_files/quote_with_giftcard_saved.php
     */
    public function testGetList()
    {
        /** @var \Magento\Sales\Model\Quote  $quote */
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();

        $data = [
            GiftCardAccount::GIFT_CARDS => ['giftcardaccount_fixture'],
            GiftCardAccount::GIFT_CARDS_AMOUNT => $quote->getGiftCardsAmount(),
            GiftCardAccount::BASE_GIFT_CARDS_AMOUNT => $quote->getBaseGiftCardsAmount(),
            GiftCardAccount::GIFT_CARDS_AMOUNT_USED => $quote->getGiftCardsAmountUsed(),
            GiftCardAccount::BASE_GIFT_CARDS_AMOUNT_USED => $quote->getBaseGiftCardsAmountUsed()
        ];
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/giftCards',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetList',
            ),
        );

        $requestData = ["cartId" => $cartId];
        $this->assertEquals($data, $this->_webApiCall($serviceInfo, $requestData));
    }
}
