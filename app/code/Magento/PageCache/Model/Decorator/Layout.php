<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\Decorator;

/**
 * Layout model
 */
class Layout extends \Magento\Core\Model\Layout
{
    /**
     * Gets HTML of block element
     *
     * @param \Magento\View\Element\AbstractBlock|bool $block
     * @return string
     * @throws \Magento\Exception
     */
    protected function _renderBlock($block)
    {
        $html = parent::_renderBlock($block);
        if ($block instanceof \Magento\View\Element\AbstractBlock && $block->isScopePrivate()) {
            $html = sprintf('<!-- BLOCK %1$s -->%2$s<!-- /BLOCK %1$s -->', $block->getNameInLayout(), $html);
        }
        return $html;
    }
}
