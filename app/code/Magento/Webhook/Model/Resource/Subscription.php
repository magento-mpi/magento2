<?php
/**
 * Webhook subscription resource
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Resource;

class Subscription extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /** @var  \Magento\Core\Model\ConfigInterface $_coreConfig */
    private $_coreConfig;

    /**
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\ConfigInterface $config
     */
    public function __construct(
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\ConfigInterface $config
    ) {
        parent::__construct($resource);
        $this->_coreConfig = $config;
    }

    /**
     * Pseudo-constructor for resource model initialization
     */
    public function _construct()
    {
        $this->_init('webhook_subscription', 'subscription_id');
    }


    /**
     * Perform actions after subscription load
     *
     * @param \Magento\Core\Model\AbstractModel $subscription
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected function _afterLoad(\Magento\Core\Model\AbstractModel $subscription)
    {
        $this->loadTopics($subscription);
        return parent::_afterLoad($subscription);
    }

    /**
     * Perform actions after subscription save
     *
     * @param \Magento\Core\Model\AbstractModel $subscription
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected function _afterSave(\Magento\Core\Model\AbstractModel $subscription)
    {
        $oldTopics = $this->_getTopics($subscription->getId());
        $this->_updateTopics($oldTopics, $subscription);
        return parent::_afterSave($subscription);
    }

    /**
     * Gets list of topics for subscription
     *
     * @param int $subscriptionId
     * @return string[]
     */
    protected function _getTopics($subscriptionId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('webhook_subscription_hook'), 'topic')
            ->where('subscription_id = ?', $subscriptionId);
        return $adapter->fetchCol($select);
    }

    /**
     * Load topics of given subscription
     *
     * @param \Magento\Core\Model\AbstractModel $subscription
     */
    public function loadTopics(\Magento\Core\Model\AbstractModel $subscription)
    {
        $subscription->setData('topics', $this->_getTopics($subscription->getId()));
    }
    /**
     * Updates list of topics for subscription
     *
     * @param array $oldTopics
     * @param \Magento\Core\Model\AbstractModel $subscription
     * @return \Magento\Webhook\Model\Resource\Subscription
     */
    protected function _updateTopics($oldTopics, \Magento\Core\Model\AbstractModel $subscription)
    {
        $newTopics = $subscription->getData('topics');
        $supportedTopics = $this->_getSupportedTopics();
        $subscriptionId = $subscription->getId();
        if (!empty($newTopics) && is_array($newTopics)) {
            if (!empty($supportedTopics) && is_array($supportedTopics)) {
                $newTopics = array_intersect($newTopics, $supportedTopics);
            }
            $intersection = array();
            if (!empty($oldTopics) && is_array($oldTopics)) {
                $intersection = array_intersect($newTopics, $oldTopics);
                $oldTopics = array_diff($oldTopics, $intersection);
            } else {
                $oldTopics = array();
            }
            $newTopics = array_diff($newTopics, $intersection);

            $this->_performTopicUpdates($oldTopics, $newTopics, $subscriptionId);
        }
        return $this;
    }

    /**
     * Get list of webhook topics defined in config.xml
     *
     * @return string[]
     */
    protected function _getSupportedTopics()
    {
        $node = $this->_coreConfig->getNode(\Magento\Webhook\Model\Source\Hook::XML_PATH_WEBHOOK);
        $availableHooks = array();
        if (!$node) {
            return $availableHooks;
        }
        foreach ($node->asArray() as $key => $hookNode) {
            foreach ($hookNode as $name => $hook) {
                if (is_array($hook)) {
                    $availableHooks[] = $key . '/' . $name;
                }
            }
            if (isset($hookNode['label'])) {
                $availableHooks[] = $key;
            }
        }
        return $availableHooks;
    }

    /**
     * Update topics for a specific subscription
     *
     * @param array $oldTopics
     * @param array $newTopics
     * @param string $subscriptionId
     */
    protected function _performTopicUpdates($oldTopics, $newTopics, $subscriptionId)
    {
        $insertData = array();

        foreach ($newTopics as $topic) {
            $insertData[] = array(
                'subscription_id' => $subscriptionId,
                'topic' => $topic
            );
        }

        if (count($oldTopics) > 0) {
            $this->_getWriteAdapter()->delete(
                $this->getTable('webhook_subscription_hook'),
                array(
                    'subscription_id = ?' => $subscriptionId,
                    'topic in (?)' => $oldTopics
                )
            );
        }

        if (count($insertData) > 0) {
            $this->_getWriteAdapter()->insertMultiple(
                $this->getTable('webhook_subscription_hook'),
                $insertData
            );
        }
    }
}
