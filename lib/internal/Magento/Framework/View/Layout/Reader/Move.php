<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Reader;

use Magento\Framework\View\Layout;

class Move implements Layout\ReaderInterface
{
    /**#@+
     * Supported types
     */
    const TYPE_MOVE = 'move';
    /**#@-*/

    /**
     * @return string[]
     */
    public function getSupportedNodes()
    {
        return [self::TYPE_MOVE];
    }

    /**
     * {@inheritdoc}
     *
     * @param Context $readerContext
     * @param Layout\Element $currentElement
     * @param Layout\Element $parentElement
     * @return $this
     */
    public function process(Context $readerContext, Layout\Element $currentElement, Layout\Element $parentElement)
    {
        $this->_scheduleMove($readerContext->getScheduledStructure(), $currentElement);
        return false;
    }

    /**
     * Schedule structural changes for move directive
     *
     * @param \Magento\Framework\View\Layout\ScheduledStructure $scheduledStructure
     * @param \Magento\Framework\View\Layout\Element $currentElement
     * @throws \Magento\Framework\Exception
     * @return $this
     */
    protected function _scheduleMove(Layout\ScheduledStructure $scheduledStructure, Layout\Element $currentElement)
    {
        $elementName = (string)$currentElement->getAttribute('element');
        $destination = (string)$currentElement->getAttribute('destination');
        $alias = (string)$currentElement->getAttribute('as') ?: '';
        if ($elementName && $destination) {
            list($siblingName, $isAfter) = $this->_beforeAfterToSibling($currentElement);
            $scheduledStructure->setElementToMove(
                $elementName,
                array($destination, $siblingName, $isAfter, $alias)
            );
        } else {
            throw new \Magento\Framework\Exception('Element name and destination must be specified.');
        }
        return $this;
    }

    /**
     * Analyze "before" and "after" information in the node and return sibling name and whether "after" or "before"
     *
     * @param \Magento\Framework\View\Layout\Element $node
     * @return array
     */
    protected function _beforeAfterToSibling($node)
    {
        $result = array(null, true);
        if (isset($node['after'])) {
            $result[0] = (string)$node['after'];
        } elseif (isset($node['before'])) {
            $result[0] = (string)$node['before'];
            $result[1] = false;
        }
        return $result;
    }
}
