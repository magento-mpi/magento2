<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Grid column widget for rendering text grid cells
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer;

class Quantity extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders quantity as integer
     *
     * @param \Magento\Framework\Object $row
     * @return int|string
     */
    public function _getValue(\Magento\Framework\Object $row)
    {
        if ($row->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return '';
        }
        $quantity = parent::_getValue($row);
        if ($row->getIsQtyDecimal()) {
            return sprintf("%01.4f", $quantity);
        } else {
            return intval($quantity);
        }
    }
}
