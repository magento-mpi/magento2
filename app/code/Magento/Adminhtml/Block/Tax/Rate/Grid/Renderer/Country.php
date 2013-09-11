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
namespace Magento\Adminhtml\Block\Tax\Rate\Grid\Renderer;

class Country extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Country
{
/**
     * Render column for export
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function renderExport(\Magento\Object $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }
}
