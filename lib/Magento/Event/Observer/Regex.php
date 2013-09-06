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
class Magento_Event_Observer_Regex extends Magento_Event_Observer
{
    /**
     * Checkes the observer's event_regex against event's name
     *
     * @param Magento_Event $event
     * @return boolean
     */
    public function isValidFor(Magento_Event $event)
    {
        return preg_match($this->getEventRegex(), $event->getName());
    }
}