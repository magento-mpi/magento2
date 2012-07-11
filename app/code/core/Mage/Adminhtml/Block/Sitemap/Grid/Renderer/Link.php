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
 * Sitemap grid link column renderer
 *
 * @category   Mage
 * @package    Mage_Sitemap
 */
class Mage_Adminhtml_Block_Sitemap_Grid_Renderer_Link extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Prepare link to display in grid
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $fileName = preg_replace('/^\//', '', $row->getSitemapPath() . $row->getSitemapFilename());
        $storeBaseUrl = Mage::app()->getStore($row->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $storeParsedUrl = parse_url($storeBaseUrl);
        $storeBaseDomain = $storeParsedUrl['scheme'] . '://' . $storeParsedUrl['host'] . '/';

        $url = $this->escapeHtml($storeBaseDomain . $fileName);

        if (file_exists(BP . DS . $fileName)) {
            return sprintf('<a href="%1$s">%1$s</a>', $url);
        }
        return $url;
    }

}
