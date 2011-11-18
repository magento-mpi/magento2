<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml airmail queue grid block action item renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Queue_Grid_Renderer_Msgtitle
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render grid row
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $msgTitle = $row->getMessageTitle();
        if (strlen($msgTitle) > 50) {
            $msgTitle = substr($msgTitle, 0, 50) . '...';
        }
        return $this->escapeHtml($msgTitle);
    }
}
