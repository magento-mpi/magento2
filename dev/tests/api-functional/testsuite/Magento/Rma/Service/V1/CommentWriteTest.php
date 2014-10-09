<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\Rma\Service\V1\Data\RmaStatusHistory;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class CommentWriteTest
 * @package Magento\Rma\Service\V1
 */
class CommentWriteTest extends WebapiAbstract
{
    /**#@+
     * Constants defined for Web Api call
     */
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'rmaCommentWriteV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture Magento/Rma/_files/rma.php
     */
    public function testAddComment()
    {
        $rma = $this->getRmaFixture();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns/' . $rma->getId() . '/comment',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'addComment'
            ]
        ];

        //$requestData = ['id'=> $order->getId(), 'statusHistory' => $commentData];
        $requestData = [
            'id' => $rma->getId(),
            'data' => [
                RmaStatusHistory::ENTITY_ID => null,
                RmaStatusHistory::COMMENT => 'Comment',
                RmaStatusHistory::CUSTOMER_NOTIFIED => false,
                RmaStatusHistory::VISIBLE_ON_FRONT => true,
                RmaStatusHistory::CREATED_AT => null,
                RmaStatusHistory::ADMIN => null,
                RmaStatusHistory::STATUS => null

            ]
        ];

        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
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
