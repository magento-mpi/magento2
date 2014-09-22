<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

use Magento\Rma\Service\V1\Data\Rma;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class RmaReadTest extends WebapiAbstract
{
    /**#@+
     * Constants defined for Web Api call
     */
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'rmaReadV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture Magento/Rma/_files/rma.php
     */
    public function testGet()
    {
        $rma = $this->getRmaFixture();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns/' . $rma->getId(),
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'get'
            ]
        ];

        $result = $this->_webApiCall($serviceInfo, ['id' => $rma->getId()]);
        $this->assertEquals($rma->getId(), $result[Rma::ENTITY_ID]);
    }

    public function testSearch()
    {
        $rma = $this->getRmaFixture();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'search'
            ]
        ];

        $request = [
            'searchCriteria' => [
                'filterGroups' => [
                    ['filters' => [['field' => Rma::ENTITY_ID, 'value' => $rma->getId(), 'conditionType' => 'eq']]]
                ]
            ]
        ];

        $result = $this->_webApiCall($serviceInfo, $request);
        $this->assertEquals($rma->getId(), $result['items'][0][Rma::ENTITY_ID]);
    }

    /**
     * Return last created Rma fixture
     *
     * @return \Magento\Rma\Model\Rma
     */
    private function getRmaFixture()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $collection = $objectManager->create('Magento\Rma\Model\Resource\Rma\Collection');
        $collection->setOrder('entity_id')
            ->setPageSize(1)
            ->load();
        return $collection->fetchItem();
    }
}
