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

class Enterprise_Staging_Block_Adminhtml_Staging_Grid_Renderer_Event
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    /**
     * Render a link to staging log entry
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $result = parent::render($row);
        return '<a href="' . $this->getUrl('*/staging_log/view',array('id'=>$row->getLogId())) . '">'
            . $this->escapeHtml($result) . '</a>';
    }

}
