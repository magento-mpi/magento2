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
class Magento_Core_Model_Layout_Argument_Processor
{
    /**
     * @var Magento_Core_Model_Layout_Argument_HandlerFactory
     */
    protected $_handlerFactory;

    /**
     * @var Magento_Core_Model_Layout_Argument_Updater
     */
    protected $_argumentUpdater;

    /**
     * Argument handlers object list
     *
     * @var array
     */
    protected $_argumentHandlers = array();

    /**
     * @param Magento_Core_Model_Layout_Argument_Updater $argumentUpdater
     * @param Magento_Core_Model_Layout_Argument_HandlerFactory $handlerFactory
     */
    public function __construct(
        Magento_Core_Model_Layout_Argument_Updater $argumentUpdater,
        Magento_Core_Model_Layout_Argument_HandlerFactory $handlerFactory
    ) {
        $this->_handlerFactory  = $handlerFactory;
        $this->_argumentUpdater = $argumentUpdater;
    }

    /**
     * Process given arguments, prepare arguments of custom type.
     * @param array $arguments
     * @throws InvalidArgumentException
     * @return array
     */
    public function process(array $arguments)
    {
        $processedArguments = array();
        foreach ($arguments as $argumentKey => $argumentValue) {
            $value = is_array($argumentValue) && isset($argumentValue['value']) ? $argumentValue['value'] : null;

            if (!in_array($argumentValue['type'], array('string', 'array'))) {
                if (!isset($value) && $argumentValue['type'] !== 'url') {
                    throw new InvalidArgumentException('Argument value is required for type ' . $argumentValue['type']);
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
     * @throws InvalidArgumentException
     * @return Magento_Core_Model_Layout_Argument_HandlerInterface
     */
    protected function _getArgumentHandler($type)
    {
        if (isset($this->_argumentHandlers[$type])) {
            return $this->_argumentHandlers[$type];
        }

        /** @var $handler Magento_Core_Model_Layout_Argument_HandlerInterface */
        $handler = $this->_handlerFactory->getArgumentHandlerByType($type);

        if (false === ($handler instanceof Magento_Core_Model_Layout_Argument_HandlerInterface)) {
            throw new InvalidArgumentException($type
                . ' type handler should implement Magento_Core_Model_Layout_Argument_HandlerInterface');
        }

        $this->_argumentHandlers[$type] = $handler;
        return $handler;
    }
}
