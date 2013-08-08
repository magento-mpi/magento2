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
 * Value object carrying page properties
 */
class Magento_Core_Model_Page
{
    /**
     * @var Magento_Core_Model_Page_Asset_Collection
     */
    private $_assets;

    /**
     * @param Magento_Core_Model_Page_Asset_Collection $assets
     */
    public function __construct(Magento_Core_Model_Page_Asset_Collection $assets)
    {
        $this->_assets = $assets;
    }

    /**
     * Retrieve collection of assets linked to a page
     *
     * @return Magento_Core_Model_Page_Asset_Collection
     */
    public function getAssets()
    {
        return $this->_assets;
    }
}
