<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Webapi\Model\Rest\Config;
use Magento\Sales\Service\V1\Data\Comment;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class CreditmemoAddCommentTest
 */
class CreditmemoAddCommentTest extends WebapiAbstract
{
    /**
     * Service read name
     */
    const SERVICE_READ_NAME = 'salesCreditmemoWriteV1';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * Creditmemo increment id
     */
    const CREDITMEMO_INCREMENT_ID = '100000001';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * Test creditmemo add comment service
     *
     * @magentoApiDataFixture Magento/Sales/_files/creditmemo_with_list.php
     */
    public function testCreditmemoAddComment()
    {
        /** @var \Magento\Sales\Model\Resource\Order\Creditmemo\Collection $creditmemoCollection */
        $creditmemoCollection = $this->objectManager->get('\Magento\Sales\Model\Resource\Order\Creditmemo\Collection');
        $creditmemo = $creditmemoCollection->getFirstItem();

        $commentData = [
            Comment::COMMENT => 'Hello world!',
            Comment::ENTITY_ID => null,
            Comment::CREATED_AT => null,
            Comment::PARENT_ID => $creditmemo->getId(),
            Comment::IS_VISIBLE_ON_FRONT => true,
            Comment::IS_CUSTOMER_NOTIFIED => true
        ];

        $requestData = ['comment' => $commentData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/creditmemo/comment',
                'httpMethod' => Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'addComment'
            ]
        ];

        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
    }
}
