<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Asset Block interface
 *
 * @category   Mage
 * @package    Magento_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Page_Block_Html_Head_AssetBlock
{
    /**
     * Get block asset
     * @return Magento_Core_Model_Page_Asset_AssetInterface
     */
    public function getAsset();
}