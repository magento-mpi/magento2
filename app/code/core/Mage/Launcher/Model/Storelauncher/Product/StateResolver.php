<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * State resolver for Product Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Product_StateResolver extends Mage_Launcher_Model_Tile_MinimalStateResolver
{
    /**
     * @var Mage_Catalog_Model_Resource_Product_Collection
     */
    protected $_productCollection;

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     */
    function __construct(
        Mage_Catalog_Model_Resource_Product_Collection $productCollection
    ) {
        // for now this collection is used only once so no cloning is needed before use
        $this->_productCollection = $productCollection;
    }

    /**
     * Resolve state
     *
     * @return bool
     */
    public function isTileComplete()
    {
        $isTileComplete = parent::isTileComplete();
        // product tile is considered to be complete if at least one product has been created
        return $isTileComplete && ($this->getProductCount() > 0);
    }

    /**
     * Get Product Count
     *
     * @return int
     */
    public function getProductCount()
    {
        return $this->_productCollection->getSize();
    }
}
