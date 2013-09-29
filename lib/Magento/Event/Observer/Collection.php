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
 * Event observer collection
 * 
 * @category   Magento
 * @package    \Magento\Event
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Event\Observer;

class Collection
{
    /**
     * Array of observers
     *
     * @var array
     */
    protected $_observers;
    
    /**
     * Initializes observers
     *
     */
    public function __construct()
    {
        $this->_observers = array();
    }
    
    /**
     * Returns all observers in the collection
     *
     * @return array
     */
    public function getAllObservers()
    {
        return $this->_observers;
    }
    
    /**
     * Returns observer by its name
     *
     * @param string $observerName
     * @return \Magento\Event\Observer
     */
    public function getObserverByName($observerName)
    {
        return $this->_observers[$observerName];
    }
    
    /**
     * Adds an observer to the collection
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Event\Observer\Collection
     */
    public function addObserver(\Magento\Event\Observer $observer)
    {
        $this->_observers[$observer->getName()] = $observer;
        return $this;
    }
    
    /**
     * Removes an observer from the collection by its name
     *
     * @param string $observerName
     * @return \Magento\Event\Observer\Collection
     */
    public function removeObserverByName($observerName)
    {
        unset($this->_observers[$observerName]);
        return $this;
    }
    
    /**
     * Dispatches an event to all observers in the collection
     *
     * @param \Magento\Event $event
     * @return \Magento\Event\Observer\Collection
     */
    public function dispatch(\Magento\Event $event)
    {
        foreach ($this->_observers as $observer) {
            $observer->dispatch($event);
        }
        return $this;
    }
}
