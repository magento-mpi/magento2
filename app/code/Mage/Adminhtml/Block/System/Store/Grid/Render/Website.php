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
 * Store render website
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Store_Grid_Render_Website
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Magento_Object $row)
    {
        return '<a title="' . Mage::helper('Mage_Core_Helper_Data')->__('Edit Web Site') . '"
            href="' . $this->getUrl('*/*/editWebsite', array('website_id' => $row->getWebsiteId())) . '">'
            . $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '</a>';
    }

}
