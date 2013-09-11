<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract index process class
 * Predefine list of methods required by indexer
 */
namespace Magento\Index\Model\Indexer;

abstract class AbstractIndexer extends \Magento\Core\Model\AbstractModel
{
    protected $_matchedEntities = array();

    /**
     * Whether the indexer should be displayed on process/list page
     *
     * @var bool
     */
    protected $_isVisible = true;

    /**
     * Get Indexer name
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * Register indexer required data inside event object
     *
     * @param   \Magento\Index\Model\Event $event
     */
    abstract protected function _registerEvent(\Magento\Index\Model\Event $event);

    /**
     * Process event based on event state data
     *
     * @param   \Magento\Index\Model\Event $event
     */
    abstract protected function _processEvent(\Magento\Index\Model\Event $event);

    /**
     * Register data required by process in event object
     *
     * @param \Magento\Index\Model\Event $event
     */
    public function register(\Magento\Index\Model\Event $event)
    {
        if ($this->matchEvent($event)) {
            $this->_registerEvent($event);
        }
        return $this;
    }

    /**
     * Process event
     *
     * @param   \Magento\Index\Model\Event $event
     * @return  \Magento\Index\Model\Indexer\AbstractIndexer
     */
    public function processEvent(\Magento\Index\Model\Event $event)
    {
        if ($this->matchEvent($event)) {
            $this->_processEvent($event);
        }
        return $this;
    }

    /**
     * Check if event can be matched by process
     *
     * @param \Magento\Index\Model\Event $event
     * @return bool
     */
    public function matchEvent(\Magento\Index\Model\Event $event)
    {
        $entity = $event->getEntity();
        $type   = $event->getType();
        return $this->matchEntityAndType($entity, $type);
    }

    /**
     * Check if indexer matched specific entity and action type
     *
     * @param   string $entity
     * @param   string $type
     * @return  bool
     */
    public function matchEntityAndType($entity, $type)
    {
        if (isset($this->_matchedEntities[$entity])) {
            if (in_array($type, $this->_matchedEntities[$entity])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Rebuild all index data
     */
    public function reindexAll()
    {
        $this->_getResource()->reindexAll();
    }

    /**
     * Try dynamicly detect and call event hanler from resource model.
     * Handler name will be generated from event entity and type code
     *
     * @param   \Magento\Index\Model\Event $event
     * @return  \Magento\Index\Model\Indexer\AbstractIndexer
     */
    public function callEventHandler(\Magento\Index\Model\Event $event)
    {
        if ($event->getEntity()) {
            $method = $event->getEntity() . '_' . $event->getType();
        } else {
            $method = $event->getType();
        }
        $method = str_replace(' ', '', ucwords(str_replace('_', ' ', $method)));

        $resourceModel = $this->_getResource();
        if (method_exists($resourceModel, $method)) {
            $resourceModel->$method($event);
        }
        return $this;
    }

    /**
     * Whether the indexer should be displayed on process/list page
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->_isVisible;
    }
}
