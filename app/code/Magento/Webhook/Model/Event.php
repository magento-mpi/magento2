<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webhook\Model;

/**
 * Stores event information in Magento database
 *
 * @method \Magento\Webhook\Model\Event setStatus()
 * @method \Magento\Webhook\Model\Event setUpdatedAt()
 * @method \Magento\Webhook\Model\Event setCreatedAt()
 */
class Event extends \Magento\Core\Model\AbstractModel implements \Magento\PubSub\EventInterface
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_dateTime = $dateTime;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize Model
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Webhook\Model\Resource\Event');
        $this->setStatus(\Magento\PubSub\EventInterface::STATUS_READY_TO_SEND);
    }

    /**
     * Prepare data to be saved to database
     *
     * @return \Magento\Webhook\Model\Event
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($this->_dateTime->formatDate(true));
        } elseif ($this->getId() && !$this->hasData('updated_at')) {
            $this->setUpdatedAt($this->_dateTime->formatDate(true));
        }
        return $this;
    }

    /**
     * Prepare data before set
     *
     * @param array $data
     * @return \Magento\Webhook\Model\Event
     */
    public function setBodyData(array $data)
    {
        return $this->setData('body_data', serialize($data));
    }

    /**
     * Prepare data before return
     *
     * @return array
     */
    public function getBodyData()
    {
        $data = $this->getData('body_data');
        if (!is_null($data)) {
            return unserialize($data);
        }
        return array();
    }

    /**
     * Prepare headers before set
     *
     * @param array $headers
     * @return \Magento\Webhook\Model\Event
     */
    public function setHeaders(array $headers)
    {
        return $this->setData('headers', serialize($headers));
    }

    /**
     * Prepare headers before return
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = $this->getData('headers');
        if (!is_null($headers)) {
            return unserialize($headers);
        }
        return array();
    }

    /**
     * Prepare options before set
     *
     * @param array $options
     * @return \Magento\Webhook\Model\Event
     */
    public function setOptions(array $options)
    {
        return $this->setData('options', serialize($options));
    }

    /**
     * Return status. Enable compatibility with interface
     *
     * @return null|int
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * Return topic and enable compatibility with interface
     *
     * @return null|string
     */
    public function getTopic()
    {
        return $this->getData('topic');
    }

    /**
     * Mark event as processed
     *
     * @return \Magento\Webhook\Model\Event
     */
    public function complete()
    {
        $this->setData('status', \Magento\PubSub\EventInterface::STATUS_PROCESSED)
            ->save();
        return $this;
    }

    /**
     * Mark event as processed
     *
     * @return \Magento\Webhook\Model\Event
     */
    public function markAsInProgress()
    {
        $this->setData('status', \Magento\PubSub\EventInterface::STATUS_IN_PROGRESS);
        return $this;
    }
}
