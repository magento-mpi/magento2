<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Event
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Event regex observer object
 * 
 * @category   Magento
 * @package    Magento_Event
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Event\Observer;

class Regex extends \Magento\Event\Observer
{
    /**
     * Checkes the observer's event_regex against event's name
     *
     * @param \Magento\Event $event
     * @return boolean
     */
    public function isValidFor(\Magento\Event $event)
    {
        return preg_match($this->getEventRegex(), $event->getName());
    }
}
