<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    \Magento\Event
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Event object and dispatcher
 *
 * @category   Magento
 * @package    \Magento\Event
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento;

class Event extends \Magento\Object
{
    /**
     * Observers collection
     *
     * @var \Magento\Event\Observer\Collection
     */
    protected $_observers;

    /**
     * Constructor
     *
     * Initializes observers collection
     *
     * @param array $data
     */
    public function __construct(array $data=array())
    {
        $this->_observers = new \Magento\Event\Observer\Collection();
        parent::__construct($data);
    }

    /**
     * Returns all the registered observers for the event
     *
     * @return \Magento\Event\Observer\Collection
     */
    public function getObservers()
    {
        return $this->_observers;
    }

    /**
     * Register an observer for the event
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Event
     */
    public function addObserver(\Magento\Event\Observer $observer)
    {
        $this->getObservers()->addObserver($observer);
        return $this;
    }

    /**
     * Removes an observer by its name
     *
     * @param string $observerName
     * @return \Magento\Event
     */
    public function removeObserverByName($observerName)
    {
        $this->getObservers()->removeObserverByName($observerName);
        return $this;
    }

    /**
     * Dispatches the event to registered observers
     *
     * @return \Magento\Event
     */
    public function dispatch()
    {
        $this->getObservers()->dispatch($this);
        return $this;
    }

    /**
     * Retrieve event name
     *
     * @return string
     */
    public function getName()
    {
        return isset($this->_data['name']) ? $this->_data['name'] : null;
    }

    public function setName($data)
    {
        $this->_data['name'] = $data;
        return $this;
    }

    public function getBlock()
    {
        return $this->_getData('block');
    }
}
