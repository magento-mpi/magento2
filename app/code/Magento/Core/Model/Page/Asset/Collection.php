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
 * List of page asset instances associated with unique identifiers
 */
namespace Magento\Core\Model\Page\Asset;

class Collection
{
    /**
     * @var \Magento\Core\Model\Page\Asset\AssetInterface[]
     */
    private $_assets = array();

    /**
     * Add an instance, identified by a unique identifier, to the list
     *
     * @param string $identifier
     * @param \Magento\Core\Model\Page\Asset\AssetInterface $asset
     */
    public function add($identifier, \Magento\Core\Model\Page\Asset\AssetInterface $asset)
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
     * @return \Magento\Core\Model\Page\Asset\AssetInterface[]
     */
    public function getAll()
    {
        return $this->_assets;
    }
}
