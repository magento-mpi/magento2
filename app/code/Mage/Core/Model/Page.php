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

    public function __construct()
    {
        $this->_assets = new Mage_Core_Model_Page_Asset_Collection();
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
