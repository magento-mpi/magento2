<?php
/**
 * Stores event information in Magento database
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method \Magento\Webhook\Model\Event setStatus()
 * @method \Magento\Webhook\Model\Event setUpdatedAt()
 * @method \Magento\Webhook\Model\Event setCreatedAt()
 */
namespace Magento\Webhook\Model;

class Event extends \Magento\Core\Model\AbstractModel implements \Magento\PubSub\EventInterface
{
    /**
     * Initialize Model
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('\Magento\Webhook\Model\Resource\Event');
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
            $this->setCreatedAt($this->_getResource()->formatDate(true));
        } elseif ($this->getId() && !$this->hasData('updated_at')) {
            $this->setUpdatedAt($this->_getResource()->formatDate(true));
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
