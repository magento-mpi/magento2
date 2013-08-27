<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface of an asset with locally accessible source file
 */
interface Magento_Core_Model_Page_Asset_LocalInterface extends Magento_Core_Model_Page_Asset_AssetInterface
{
    /**
     * Retrieve source file
     *
     * @return string
     */
    public function getSourceFile();
}
