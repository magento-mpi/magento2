<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Payment\Block\Info;

/**
 * Substitution payment info
 */
class Substitution extends \Magento\Payment\Block\Info
{
    /**
     * Add additional info block
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $parentBlock = $this->getParentBlock();
        if (!$parentBlock) {
            return $this;
        }

        $container = $parentBlock->getParentBlock();
        if ($container) {
            $block = $this->_layout->createBlock(
                'Magento\Framework\View\Element\Template',
                '',
                ['data' => ['method' => $this->getMethod(), 'template' => 'Magento_Payment::info/substitution.phtml']]
            );
            $container->setChild('order_payment_additional', $block);
        }
        return $this;
    }
}
