<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Reader;

use Magento\Framework\View\Layout;

class Remove implements Layout\ReaderInterface
{
    /**#@+
     * Supported types
     */
    const TYPE_REMOVE = 'remove';
    /**#@-*/

    /**
     * @return string[]
     */
    public function getSupportedNodes()
    {
        return [self::TYPE_REMOVE];
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
        $scheduledStructure = $readerContext->getScheduledStructure();
        $scheduledStructure->setElementToRemoveList((string)$currentElement->getAttribute('name'));
        return false;
    }
}
