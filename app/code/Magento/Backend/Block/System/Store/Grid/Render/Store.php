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
 * Store render store
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\System\Store\Grid\Render;

class Store
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Object $row)
    {
        if (!$row->getData($this->getColumn()->getIndex())) {
            return null;
        }
        return '<a title="' . __('Edit Store View') . '"
            href="' . $this->getUrl('adminhtml/*/editStore', array('store_id' => $row->getStoreId())) . '">'
            . $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '</a>';
    }
}
