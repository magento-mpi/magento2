<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\PromotionPermissions\Block\Adminhtml;

class Plugin
{
    /**
     * @var string[]
     */
    protected $restrictedButtons = [];

    /**
     * Edit Sales Rules flag
     *
     * @var boolean
     */
    protected $canEdit = true;

    /**
     * Check where button can be rendered
     *
     * @param \Magento\Backend\Block\Widget\Button\ContextInterface $subject
     * @param callable $proceed
     * @param \Magento\Backend\Block\Widget\Button\Item $item
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function canRender(
        \Magento\Backend\Block\Widget\Button\ContextInterface $subject,
        \Closure $proceed,
        \Magento\Backend\Block\Widget\Button\Item $item
    ) {
        $result = $proceed($item);
        if ($result && !$this->canEdit) {
            $result = !in_array($item->getId(), $this->restrictedButtons);
        }
        return $result;
    }
}
