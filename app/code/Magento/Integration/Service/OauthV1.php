<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service;

use Magento\Integration\Model\Oauth\Consumer as ConsumerModel;
use Magento\Integration\Model\Oauth\Consumer\Factory as ConsumerFactory;

/**
 * Integration Oauth Service.
 *
 * This service is used to manage oauth consumer data
 */
class OauthV1 implements OauthV1Interface
{
    /** @var ConsumerFactory */
    protected $_consumerFactory;

    /**
     * Construct and initialize Consumer Factory
     *
     * @param ConsumerFactory $consumerFactory
     */
    public function __construct(ConsumerFactory $consumerFactory)
    {
        $this->_consumerFactory = $consumerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteConsumer($consumerId)
    {
        $consumer = $this->_loadConsumerById($consumerId);
        $data = $consumer->getData();
        $consumer->delete();
        return $data;
    }

    /**
     * Load consumer by id.
     *
     * @param int $consumerId
     * @return ConsumerModel
     * @throws \Magento\Integration\Exception
     */
    protected function _loadConsumerById($consumerId)
    {
        $consumer = $this->_consumerFactory->create()->load($consumerId);
        if (!$consumer->getId()) {
            throw new \Magento\Integration\Exception(__("Consumer with ID '%1' does not exist.", $consumerId));
        }
        return $consumer;
    }
} 