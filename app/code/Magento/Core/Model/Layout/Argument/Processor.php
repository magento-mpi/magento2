<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout argument processor
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Layout\Argument;

class Processor
{
    /**
     * @var \Magento\Core\Model\Layout\Argument\HandlerFactory
     */
    protected $_handlerFactory;

    /**
     * @var \Magento\Core\Model\Layout\Argument\Updater
     */
    protected $_argumentUpdater;

    /**
     * Argument handlers object list
     *
     * @var array
     */
    protected $_argumentHandlers = array();

    /**
     * @param \Magento\Core\Model\Layout\Argument\Updater $argumentUpdater
     * @param \Magento\Core\Model\Layout\Argument\HandlerFactory $handlerFactory
     */
    public function __construct(
        \Magento\Core\Model\Layout\Argument\Updater $argumentUpdater,
        \Magento\Core\Model\Layout\Argument\HandlerFactory $handlerFactory
    ) {
        $this->_handlerFactory  = $handlerFactory;
        $this->_argumentUpdater = $argumentUpdater;
    }

    /**
     * Process given arguments, prepare arguments of custom type.
     * @param array $arguments
     * @throws \InvalidArgumentException
     * @return array
     */
    public function process(array $arguments)
    {
        $processedArguments = array();
        foreach ($arguments as $argumentKey => $argumentValue) {
            $value = is_array($argumentValue) && isset($argumentValue['value']) ? $argumentValue['value'] : null;

            if (!in_array($argumentValue['type'], array('string', 'array', 'helper'))) {
                if (!isset($value) && $argumentValue['type'] !== 'url') {
                    throw new \InvalidArgumentException('Argument value is required for type ' . $argumentValue['type']);
                }

                $handler = $this->_getArgumentHandler($argumentValue['type']);
                $value   = $handler->process($value);
            }

            if (!empty($argumentValue['updater'])) {
                $value = $this->_argumentUpdater->applyUpdaters($value, $argumentValue['updater']);
            }
            $processedArguments[$argumentKey] = $value;
        }
        return $processedArguments;
    }

    /**
     * Get argument handler by type
     *
     * @param string $type
     * @throws \InvalidArgumentException
     * @return \Magento\Core\Model\Layout\Argument\HandlerInterface
     */
    protected function _getArgumentHandler($type)
    {
        if (isset($this->_argumentHandlers[$type])) {
            return $this->_argumentHandlers[$type];
        }

        /** @var $handler \Magento\Core\Model\Layout\Argument\HandlerInterface */
        $handler = $this->_handlerFactory->getArgumentHandlerByType($type);

        if (false === ($handler instanceof \Magento\Core\Model\Layout\Argument\HandlerInterface)) {
            throw new \InvalidArgumentException($type
            . ' type handler should implement \Magento\Core\Model\Layout\Argument\HandlerInterface');
        }

        $this->_argumentHandlers[$type] = $handler;
        return $handler;
    }
}
