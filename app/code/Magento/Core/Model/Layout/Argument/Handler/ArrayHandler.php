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
 * Layout argument. Type Array
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Layout\Argument\Handler;

class ArrayHandler extends \Magento\Core\Model\Layout\Argument\HandlerAbstract
{
    /**
     * @var \Magento\Core\Model\Layout\Argument\HandlerFactory
     */
    protected $_handlerFactory;

    /**
     * @param \Magento\Core\Model\Layout\Argument\HandlerFactory $handlerFactory
     */
    public function __construct(
        \Magento\Core\Model\Layout\Argument\HandlerFactory $handlerFactory
    ) {
        $this->_handlerFactory = $handlerFactory;
    }

    /**
     * Process Array argument
     *
     * @param array $argument
     * @throws \InvalidArgumentException
     * @return array
     */
    public function process(array $argument)
    {
        $this->_validate($argument);
        $result = array();
        foreach ($argument['value'] as $name => $item) {
            $result[$name] = $this->_handlerFactory
                ->getArgumentHandlerByType($item['type'])
                ->process($item);
        }
        return $result;
    }

    /**
     * @param array $argument
     * @throws \InvalidArgumentException
     */
    protected function _validate(array $argument)
    {
        parent::_validate($argument);
        $items = $argument['value'];
        if (!is_array($items)) {
            throw new \InvalidArgumentException(
                'Passed value has incorrect format. ' . $this->_getArgumentInfo($argument)
            );
        }
        foreach ($items as $name => $item) {
            if (!is_array($item) || !isset($item['type']) || !isset($item['value'])) {
                throw new \InvalidArgumentException(
                    'Array item: "' . $name . '" has incorrect format. ' . $this->_getArgumentInfo($argument)
                );
            }
        }
    }

    /**
     * Retrive value from Array argument
     *
     * @param \Magento\Core\Model\Layout\Element $argument
     * @return array|null
     */
    protected function _getArgumentValue(\Magento\Core\Model\Layout\Element $argument)
    {
        $items = $argument->xpath('item');
        if ($this->_isUpdater($argument) && empty($items)) {
            return null;
        }
        $result = array();
        /** @var $item \Magento\Core\Model\Layout\Element */
        foreach ($items as $item) {
            $itemName = (string)$item['name'];
            $itemType = $this->_getArgumentType($item);
            $result[$itemName] = $this->_handlerFactory
                ->getArgumentHandlerByType($itemType)
                ->parse($item);
        }
        return $result;
    }
}
