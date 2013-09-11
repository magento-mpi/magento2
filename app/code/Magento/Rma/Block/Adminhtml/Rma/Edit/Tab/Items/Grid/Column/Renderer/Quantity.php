<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column widget for rendering text grid cells
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer;

class Quantity
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders quantity as integer
     *
     * @param \Magento\Object $row
     * @return int|string
     */
    public function _getValue(\Magento\Object $row)
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
