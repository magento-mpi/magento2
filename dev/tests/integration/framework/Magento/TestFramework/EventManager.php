<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Basic implementation of the message exchange mechanism known as pub/sub messaging pattern.
 * Terminology deviations:
 *   event            - message
 *   event manager    - message publisher
 *   fire event       - publish message
 *   event subscriber - message subscriber
 */
class Magento_TestFramework_EventManager
{
    /**
     * Registered event subscribers
     *
     * @var array
     */
    protected $_subscribers = array();

    /**
     * Constructor
     *
     * @param array $subscribers Subscriber instances
     */
    public function __construct(array $subscribers)
    {
        $this->_subscribers = $subscribers;
    }

    /**
     * Notify registered subscribers, which are interested in event
     *
     * @param string $eventName
     * @param array $parameters Parameters to be passed to each subscriber
     * @param bool $reverseOrder Whether subscribers should be notified in reverse order
     */
    public function fireEvent($eventName, array $parameters = array(), $reverseOrder = false)
    {
        $subscribers = ($reverseOrder ? array_reverse($this->_subscribers) : $this->_subscribers);
        foreach ($subscribers as $subscriberInstance) {
            $callback = array($subscriberInstance, $eventName);
            if (is_callable($callback)) {
                call_user_func_array($callback, $parameters);
            }
        }
    }
}
