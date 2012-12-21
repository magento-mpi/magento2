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
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return mixed
     */
    public function _getValue(Varien_Object $row)
    {
        $url7zip = $this->helper('Mage_Adminhtml_Helper_Data')
            ->__('The archive can be uncompressed with <a href="%s">%s</a> on Windows systems', 'http://www.7-zip.org/',
            '7-Zip');

        $format = '<a href="' . $this->getUrl('*/*/download', array('time' => '$time', 'type' => '$type'))
                  . '">$extension</a> &nbsp; <small>('.$url7zip.')</small>';
        if (preg_match_all($this->_variablePattern, $format, $matches)) {
            // Parsing of format string
            $formattedString = $format;
            foreach ($matches[0] as $matchIndex=>$match) {
                $value = $row->getData($matches[1][$matchIndex]);
                $formattedString = str_replace($match, $value, $formattedString);
            }
            return $formattedString;
        }

        return '';
    }
}