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

class Magento_Adminhtml_Block_System_Store_Grid_Render_Store
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Magento_Object $row)
    {
        if (!$row->getData($this->getColumn()->getIndex())) {
            return null;
        }
        return '<a title="' . __('Edit Store View') . '"
            href="' . $this->getUrl('*/*/editStore', array('store_id' => $row->getStoreId())) . '">'
            . $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '</a>';
    }
}
