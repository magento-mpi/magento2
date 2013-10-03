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
     * Parse given argument
     *
     * @param \Magento\Core\Model\Layout\Element $argument
     * @throws \InvalidArgumentException
     * @return array
     */
    public function parse(\Magento\Core\Model\Layout\Element $argument)
    {
        $type = $this->_getArgumentType($argument);
        $handler = $this->_handlerFactory->getArgumentHandlerByType($type);
        return $handler->parse($argument);
    }

    /**
     * Process given argument
     *
     * @param array $argument
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function process(array $argument)
    {
        $handler = $this->_handlerFactory->getArgumentHandlerByType($argument['type']);
        $result = $handler->process($argument);
        if (!empty($argument['updaters'])) {
            $result = $this->_argumentUpdater->applyUpdaters($result, $argument['updaters']);
        }
        return $result;
    }

    /**
     * Get Argument's XSI type
     *
     * @param \Magento\Core\Model\Layout\Element $argument
     * @return string
     */
    protected function _getArgumentType(\Magento\Core\Model\Layout\Element $argument)
    {
        return (string)$argument->attributes('xsi', true)->type;
    }
}
