<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout argument processor
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Layout_Argument_Processor
{
    /**
     * @var Mage_Core_Model_Layout_Argument_HandlerFactory
     */
    protected $_handlerFactory;

    /**
     * @var Mage_Core_Model_Layout_Argument_Updater
     */
    protected $_argumentUpdater;

    /**
     * Argument handlers object list
     *
     * @var array
     */
    protected $_argumentHandlers = array();

    /**
     * @param Mage_Core_Model_Layout_Argument_Updater $argumentUpdater
     * @param Mage_Core_Model_Layout_Argument_HandlerFactory $handlerFactory
     */
    public function __construct(
        Mage_Core_Model_Layout_Argument_Updater $argumentUpdater,
        Mage_Core_Model_Layout_Argument_HandlerFactory $handlerFactory
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
            $value = isset($argumentValue['value']) ? $argumentValue['value'] : null;

            if (!empty($argumentValue['type'])) {
                if (empty($value)) {
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
     * @return Mage_Core_Model_Layout_Argument_HandlerInterface
     */
    protected function _getArgumentHandler($type)
    {
        if (isset($this->_argumentHandlers[$type])) {
            return $this->_argumentHandlers[$type];
        }

        /** @var $handler Mage_Core_Model_Layout_Argument_HandlerInterface */
        $handler = $this->_handlerFactory->getArgumentHandlerByType($type);

        if (!($handler instanceof Mage_Core_Model_Layout_Argument_HandlerInterface)) {
            throw new InvalidArgumentException($type
                . ' type handler should implement Mage_Core_Model_Layout_Argument_HandlerInterface');
        }

        $this->_argumentHandlers[$type] = $handler;
        return $handler;
    }
}
