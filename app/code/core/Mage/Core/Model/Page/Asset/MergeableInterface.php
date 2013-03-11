<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Mage_Core_Model_Page_Asset_MergeableInterface extends Mage_Core_Model_Page_Asset_AssetInterface
{
    /**
     * Retrieve file, contents of which is to be merged
     *
     * @return string
     */
    public function getSourceFile();
}
