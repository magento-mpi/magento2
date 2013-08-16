<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tax rates grid item renderer country
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Tax_Rate_Grid_Renderer_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Country
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
