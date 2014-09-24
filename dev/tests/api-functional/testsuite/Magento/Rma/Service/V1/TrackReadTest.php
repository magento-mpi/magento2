<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class TrackReadTest extends WebapiAbstract
{
    /**#@+
     * Constants defined for Web Api call
     */
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'rmaTrackReadV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture Magento/Rma/_files/rma.php
     */
    public function testGetTracks()
    {
        $rma = $this->getRmaFixture();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns/' . $rma->getId() . '/tracking-numbers',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getTracks'
            ]
        ];

        $items = $this->_webApiCall($serviceInfo, ['id' => $rma->getId()]);
        $this->assertNotEmpty($items);

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
