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
 * Adminhtml tax rates grid item renderer country
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Tax_Rate_Grid_Renderer_Country extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Country
{
/**
     * Render column for export
     *
     * @param Magento_Object $row
     * @return string
     */
    public function renderExport(Magento_Object $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }
}
