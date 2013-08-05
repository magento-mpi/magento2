<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml Google Shopping Item Id Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Block_Adminhtml_Items_Renderer_Id
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders Google Shopping Item Id
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $baseUrl = 'http://www.google.com/merchants/view?docId=';

        $itemUrl = $row->getData($this->getColumn()->getIndex());
        $urlParts = parse_url($itemUrl);
        if (isset($urlParts['path'])) {
            $pathParts = explode('/', $urlParts['path']);
            $itemId = $pathParts[count($pathParts) - 1];
        } else {
            $itemId = $itemUrl;
        }
        $title = __('View Item in Google Content');

        return sprintf('<a href="%s" alt="%s" title="%s" target="_blank">%s</a>', $baseUrl . $itemId, $title, $title, $itemId);
    }
}
