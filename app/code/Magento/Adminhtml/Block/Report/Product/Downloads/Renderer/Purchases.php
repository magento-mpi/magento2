<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Product Downloads Purchases Renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Report\Product\Downloads\Renderer;

class Purchases
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders Purchases value
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        if ( ($value = $row->getData($this->getColumn()->getIndex())) > 0) {
            return $value;
        }
        return __('Unlimited');
    }
}
