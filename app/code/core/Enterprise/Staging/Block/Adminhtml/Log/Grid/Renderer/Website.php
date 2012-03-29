<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter queue grid block action item renderer
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Staging_Block_Adminhtml_Log_Grid_Renderer_Website
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render website name for log entry
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $deleted = false;
        if ($this->getColumn()->getIndex() == 'staging_website_name') {
            $result = $this->escapeHtml($row->getStagingWebsiteName());
            if ($row->getStagingWebsiteId() === null && $result !== null) {
                $deleted = true;
            }
        }
        else {
            $result = $this->escapeHtml($row->getMasterWebsiteName());
            if ($row->getMasterWebsiteId() === null && $result !== null) {
                $deleted = true;
            }
        }
        if ($deleted) {
            $result .= ' ' . Mage::helper('Enterprise_Staging_Helper_Data')->__('[deleted]');
        }
        return $result;
    }

}
