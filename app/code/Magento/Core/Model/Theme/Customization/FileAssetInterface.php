<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme asset file interface
 */
interface Magento_Core_Model_Theme_Customization_FileAssetInterface
{
    /**
     * Get content type of file
     *
     * @return string
     */
    public function getContentType();
}
