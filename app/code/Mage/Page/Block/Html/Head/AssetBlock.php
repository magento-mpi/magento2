<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Asset Block interface
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Page_Block_Html_Head_AssetBlock
{
    /**
     * Get block asset
     * @return Mage_Core_Model_Page_Asset_AssetInterface
     */
    public function getAsset();
}