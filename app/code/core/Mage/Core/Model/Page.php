<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Page
{
    /**
     * @var Mage_Core_Model_Asset_Collection
     */
    private $_assets;

    public function __construct()
    {
        $this->_assets = new Mage_Core_Model_Asset_Collection();
    }

    /**
     * Retrieve collection of instances
     *
     * @return Mage_Core_Model_Asset_Collection
     */
    public function getAssets()
    {
        return $this->_assets;
    }
}
