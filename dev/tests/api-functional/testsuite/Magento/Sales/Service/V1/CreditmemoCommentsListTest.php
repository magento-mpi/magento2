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
use Magento\Sales\Api\Data\CreditmemoCommentInterface;

/**
 * Class CreditmemoCommentsListTest
 */
class CreditmemoCommentsListTest extends WebapiAbstract
{
    const SERVICE_NAME = 'salesCreditmemoManagementV1';

    const SERVICE_VERSION = 'V1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/creditmemo_for_get.php
     */
    public function testCreditmemoCommentsList()
    {
        $comment = 'Test comment';
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Sales\Model\Resource\Order\Creditmemo\Collection $creditmemoCollection */
        $creditmemoCollection = $objectManager->get('\Magento\Sales\Model\Resource\Order\Creditmemo\Collection');
        $creditmemo = $creditmemoCollection->getFirstItem();
        $creditmemoComment = $objectManager->get('\Magento\Sales\Model\Order\Creditmemo\Comment');

        $commentData = [
            CreditmemoCommentInterface::COMMENT => 'Hello world!',
            CreditmemoCommentInterface::ENTITY_ID => null,
            CreditmemoCommentInterface::CREATED_AT => null,
            CreditmemoCommentInterface::PARENT_ID => $creditmemo->getId(),
            CreditmemoCommentInterface::IS_VISIBLE_ON_FRONT => true,
            CreditmemoCommentInterface::IS_CUSTOMER_NOTIFIED => true
        ];
        $creditmemoComment->setData($commentData)->save();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/creditmemo/' . $creditmemo->getId() . '/comments',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getCommentsList'
            ]
        ];
        $requestData = ['id' => $creditmemo->getId()];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        // TODO Test fails, due to the inability of the framework API to handle data collection
        $this->assertNotEmpty($result);
        foreach ($result['items'] as $item) {
            $comment = $objectManager->get('Magento\Sales\Model\Order\Creditmemo\Comment')->load($item['entity_id']);
            $this->assertEquals($comment->getComment(), $item['comment']);
        }
    }
}
