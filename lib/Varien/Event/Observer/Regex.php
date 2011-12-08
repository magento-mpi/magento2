<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Event
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Event regex observer object
 * 
 * @category   Varien
 * @package    Varien_Event
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Event_Observer_Regex extends Varien_Event_Observer
{
    /**
     * Checkes the observer's event_regex against event's name
     *
     * @param Varien_Event $event
     * @return boolean
     */
    public function isValidFor(Varien_Event $event)
    {
        return preg_match($this->getEventRegex(), $event->getName());
    }
}