<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tax rates grid item renderer country
 *
 * @category   Magento
 * @package     Magento_Tax
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Block\Adminhtml\Rate\Grid\Renderer;

class Country extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Country
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
