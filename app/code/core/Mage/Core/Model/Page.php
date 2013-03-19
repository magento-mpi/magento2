<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Value object carrying page properties
 */
class Mage_Core_Model_Page
{
    /**
     * @var Mage_Core_Model_Page_Asset_Collection
     */
    private $_assets;

    /**
     * @param Mage_Core_Model_Page_Asset_Collection $assets
     */
    public function __construct(Mage_Core_Model_Page_Asset_Collection $assets)
    {
        $this->_assets = $assets;
    }

    /**
     * Retrieve collection of assets linked to a page
     *
     * @return Mage_Core_Model_Page_Asset_Collection
     */
    public function getAssets()
    {
        return $this->_assets;
    }
}
