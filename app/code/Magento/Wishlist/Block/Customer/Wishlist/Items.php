<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Block\Customer\Wishlist;

/**
 * Wishlist block customer items
 */
class Items extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve table column object list
     *
     * @return \Magento\Wishlist\Block\Customer\Wishlist\Item\Column[]
     */
    public function getColumns()
    {
        $columns = array();
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $child) {
            if ($child instanceof \Magento\Wishlist\Block\Customer\Wishlist\Item\Column && $child->isEnabled()) {
                $columns[] = $child;
            }
        }
        return $columns;
    }
}
