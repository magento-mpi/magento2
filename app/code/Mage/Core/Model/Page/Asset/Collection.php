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
 * List of page asset instances associated with unique identifiers
 */
class Mage_Core_Model_Page_Asset_Collection
{
    /**
     * @var Mage_Core_Model_Page_Asset_AssetInterface[]
     */
    private $_assets = array();

    /**
     * Add an instance, identified by a unique identifier, to the list
     *
     * @param string $identifier
     * @param Mage_Core_Model_Page_Asset_AssetInterface $asset
     */
    public function add($identifier, Mage_Core_Model_Page_Asset_AssetInterface $asset)
    {
        $this->_assets[$identifier] = $asset;
    }

    /**
     * Whether an item belongs to a collection or not
     *
     * @param string $identifier
     * @return bool
     */
    public function has($identifier)
    {
        return isset($this->_assets[$identifier]);
    }

    /**
     * Remove an item from the list
     *
     * @param string $identifier
     */
    public function remove($identifier)
    {
        unset($this->_assets[$identifier]);
    }

    /**
     * Retrieve all items in the collection
     *
     * @return Mage_Core_Model_Page_Asset_AssetInterface[]
     */
    public function getAll()
    {
        return $this->_assets;
    }
}
