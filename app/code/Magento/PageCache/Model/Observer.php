<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model;

/**
 * Class Observer
 * @package Magento\PageCache\Model
 */
class Observer
{
    /**
     * @param \Magento\Event\Observer $observer
     */
    public function processLayoutRenderElement(\Magento\Event\Observer $observer)
    {
        /** @var \Magento\Core\Model\Layout $layout */
        $layout = $observer->getEvent()->getLayout();
        if ($layout->isCacheable()) {
            $name = $observer->getEvent()->getElementName();
            $block = $layout->getBlock($name);
            if ($block instanceof \Magento\View\Element\AbstractBlock && $block->isScopePrivate()) {
                $transport = $observer->getEvent()->getTransport();
                $html = $transport->getData('output');
                $html = sprintf('<!-- BLOCK %1$s -->%2$s<!-- /BLOCK %1$s -->', $block->getNameInLayout(), $html);
                $transport->setData('output', $html);
            }
        }
    }
}
