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
 * Adminhtml sales create order product search grid price column renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Create\Search\Grid\Renderer;

class Price extends
    \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Price
{
    /**
     * Render minimal price for downloadable products
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        if ($row->getTypeId() == 'downloadable') {
            $row->setPrice($row->getPrice());
        }
        return parent::render($row);
    }

}
