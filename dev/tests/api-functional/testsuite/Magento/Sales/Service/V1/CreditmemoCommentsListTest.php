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

class CreditmemoCommentsListTest extends WebapiAbstract
{
    const SERVICE_NAME = 'salesCreditmemoCommentsListV1';

    const SERVICE_VERSION = 'V1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/creditmemo.php
     */
    public function testCreditmemoCommentsList()
    {
        $comment = 'Test comment';
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $objectManager->get('Magento\Sales\Model\Order\Creditmemo')->load('100000001', 'increment_id');
        $creditmemo->addComment($comment);
        $creditmemo->save();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/creditmemo/' . $creditmemo->getId() . '/comments',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'invoke'
            ]
        ];
        $requestData = ['id' => $creditmemo->getId()];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        foreach ($result['items'] as $item) {
            $comment = $objectManager->get('Magento\Sales\Model\Order\Creditmemo\Comment')
                ->load($item['entity_id']);
            $this->assertEquals($comment->getComment(), $item['comment']);
        }
    }
}
