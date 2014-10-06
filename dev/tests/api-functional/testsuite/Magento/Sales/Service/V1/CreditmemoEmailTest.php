<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class CreditmemoEmailTest
 *
 * @package Magento\Sales\Service\V1
 */
class CreditmemoEmailTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';

    const SERVICE_NAME = 'salesCreditmemoWriteV1';

    const CREDITMEMO_INCREMENT_ID = '100000001';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/creditmemo_with_list.php
     */
    public function testCreditmemoEmail()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Sales\Model\Resource\Order\Creditmemo\Collection $creditmemoCollection */
        $creditmemoCollection = $objectManager->get('\Magento\Sales\Model\Resource\Order\Creditmemo\Collection');
        $creditmemo = $creditmemoCollection->getFirstItem();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/creditmemo/' . $creditmemo->getId(),
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'email'
            ]
        ];
        $requestData = ['id' => $creditmemo->getId()];
        $this->_webApiCall($serviceInfo, $requestData);
    }
}
