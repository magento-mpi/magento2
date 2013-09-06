<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Logging archive grid  item renderer
 *
 * @category   Magento
 * @package    Magento_Logging
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Logging_Block_Adminhtml_Grid_Renderer_Download
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Renders grid column
     *
     * @param Magento_Object $row
     *
     * @return mixed
     */
    public function _getValue(Magento_Object $row)
    {
        return '<a href="' . $this->getUrl('*/*/download', array('basename' => $row->getBasename())) . '">'
               . $row->getBasename() . '</a>';

    }
}
