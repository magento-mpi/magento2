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
 * Store render website
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_System_Store_Grid_Render_Website
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Magento_Object $row)
    {
        return '<a title="' . __('Edit Web Site') . '"
            href="' . $this->getUrl('*/*/editWebsite', array('website_id' => $row->getWebsiteId())) . '">'
            . $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '</a>';
    }

}
