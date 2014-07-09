<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdminGws\Model;

class CallbackList
{
    /**
     * @var array
     */
    protected $_callbacks = array();

    /**
     * @var \Magento\Framework\Stdlib\String
     */
    protected $string;

    /**
     * @var \Magento\AdminGws\Model\ConfigInterface
     */
    protected $config;

    /**
     * @var CallbackBuilder
     */
    protected $callbackBuilder;

    /**
     * @param \Magento\Framework\Stdlib\String $string
     * @param ConfigInterface $config
     * @param CallbackBuilder $callbackBuilder
     */
    public function __construct(
        \Magento\Framework\Stdlib\String $string,
        \Magento\AdminGws\Model\ConfigInterface $config,
        CallbackBuilder $callbackBuilder
    ) {
        $this->string = $string;
        $this->config = $config;
        $this->callbackBuilder = $callbackBuilder;
    }

    /**
     * Get a limiter callback for an instance from mappers configuration
     *
     * @param string $callbackGroup (collection, model)
     * @param object $instance
     * @return null|string|bool
     */
    public function pickCallback($callbackGroup, $instance)
    {
        if (!($instanceClass = get_class($instance))) {
            return null;
        }

        // gather callbacks from mapper configuration
        if (!isset($this->_callbacks[$callbackGroup])) {
            $this->_callbacks[$callbackGroup] = array();
            foreach ($this->config->getCallbacks($callbackGroup) as $className => $callback) {
                $this->_callbacks[$callbackGroup][$className] = $this->callbackBuilder->build($callback);
            }
        }

        /**
         * Determine callback for current instance
         * Explicit class name has priority before inherited classes
         */
        $result = false;
        if (isset($this->_callbacks[$callbackGroup][$instanceClass])) {
            $result = $this->_callbacks[$callbackGroup][$instanceClass];
        } else {
            foreach ($this->_callbacks[$callbackGroup] as $className => $callback) {
                if ($instance instanceof $className) {
                    $result = $callback;
                    break;
                }
            }
        }
        return $result;
    }
}
