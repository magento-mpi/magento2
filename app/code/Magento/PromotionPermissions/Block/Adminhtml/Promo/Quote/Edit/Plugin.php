<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\PromotionPermissions\Block\Adminhtml\Promo\Quote\Edit;

class Plugin extends \Magento\PromotionPermissions\Block\Adminhtml\Plugin
{
    /**
     * @var string[]
     */
    protected $restrictedButtons = ['delete', 'save', 'save_and_continue_edit', 'reset'];

    /**
     * @param \Magento\PromotionPermissions\Helper\Data $promoPermData
     */
    public function __construct(\Magento\PromotionPermissions\Helper\Data $promoPermData)
    {
        $this->canEdit = $promoPermData->getCanAdminEditSalesRules();
    }

    /**
     * Check where button can be rendered
     *
     * @param \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit $subject
     * @param callable $proceed
     * @param \Magento\Backend\Block\Widget\Button\Item $item
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCanRender(
        \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit $subject,
        \Closure $proceed,
        \Magento\Backend\Block\Widget\Button\Item $item
    ) {
        return $this->canRender($subject, $proceed, $item);
    }
}
