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
 * Adminhtml Report Products Reviews renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Grid_Column_Renderer_Product
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $id   = $row->getId();

        return sprintf('<a href="%s">%s</a>',
            $this->getUrl('*/catalog_product_review/', array('productId' => $id)),
            Mage::helper('Magento_Adminhtml_Helper_Data')->__('Show Reviews')
        );
    }
}
