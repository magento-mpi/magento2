<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Service\V1;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\GiftMessage\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * @var \Magento\GiftMessage\Service\V1\Data\MessageBuilder
     */
    protected $builder;

    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\GiftMessage\Model\MessageFactory $messageFactory,
        \Magento\GiftMessage\Service\V1\Data\MessageBuilder $builder
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->storeManager = $storeManager;
        $this->messageFactory = $messageFactory;
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {

        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);

        $messageId = $quote->getGiftMessageId();
        if (!$messageId) {
            return null;
        }

        /** @var \Magento\GiftMessage\Model\Message $model */
        $model = $this->messageFactory->create()->load($messageId);

        $this->builder->setId($model->getId());
        $this->builder->setRecipient($model->getRecipient());
        $this->builder->setSender($model->getSender());
        $this->builder->setMessage($model->getMessage());

        return $this->builder->create();
    }
}
