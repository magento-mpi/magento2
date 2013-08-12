<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup grid item renderer
 *
 * @category   Mage
 * @package    Mage_Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Block_Adminhtml_Grid_Column_Renderer_Download
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Renders grid column
     *
     * @param Magento_Object $row
     * @return mixed
     */
    public function _getValue(Magento_Object $row)
    {
        $url7zip = $this->helper('Magento_Adminhtml_Helper_Data')
            ->__('The archive can be uncompressed with <a href="%s">%s</a> on Windows systems.', 'http://www.7-zip.org/',
            '7-Zip');

        return '<a href="' . $this->getUrl('*/*/download',
            array('time' => $row->getData('time'), 'type' => $row->getData('type'))) . '">' . $row->getData('extension')
               . '</a> &nbsp; <small>(' . $url7zip . ')</small>';

    }
}
