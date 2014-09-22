<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

use Magento\Rma\Service\V1\Data\Track;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class TrackWriteTest extends WebapiAbstract
{
    /**#@+
     * Constants defined for Web Api call
     */
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'rmaTrackWriteV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture Magento/Rma/_files/rma.php
     */
    public function testAddTrack()
    {
        $rma = $this->getRmaFixture();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns/' . $rma->getId() . '/tracking-numbers',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'addTrack'
            ]
        ];

        $requestData = [
            'id' => $rma->getId(),
            'track' => [
                Track::TRACK_NUMBER => 'Track Number',
                Track::CARRIER_TITLE => 'Carrier title',
                Track::CARRIER_CODE => 'custom'
            ]
        ];

        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
    }

    public function testRemoveTrackById()
    {
        $rma = $this->getRmaFixture();
        $track = $rma->getTrackingNumbers()->load()->fetchItem();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns/' . $rma->getId() . '/tracking-numbers/' . $track->getId(),
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'removeTrackById'
            ]
        ];

        $this->assertTrue($this->_webApiCall($serviceInfo, ['id' => $track->getId()]));
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
