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

class Observer
{
    /**
     * @param \Magento\Event\Observer $observer
     */
    public function processLayoutRenderElement(\Magento\Event\Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
        $name = $observer->getEvent()->getElementName();
        $transport = $observer->getEvent()->getTransport();

        $block = $layout->getBlock($name);
        if ($block instanceof \Magento\View\Element\AbstractBlock && $block->isScopePrivate()) {
            $html = $transport->getData('output');
            $html = sprintf('<!-- BLOCK %1$s -->%2$s<!-- /BLOCK %1$s -->', $block->getNameInLayout(), $html);
            $transport->setData('output', $html);
        }
    }
}
