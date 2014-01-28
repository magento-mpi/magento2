<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Product Downloads Purchases Renderer
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Adminhtml\Product\Downloads\Renderer;

class Purchases
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders Purchases value
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        if ( ($value = $row->getData($this->getColumn()->getIndex())) > 0) {
            return $value;
        }
        return __('Unlimited');
    }
}
