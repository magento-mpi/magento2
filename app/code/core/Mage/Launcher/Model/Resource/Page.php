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
 * Landing page resource model
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Resource_Page extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Tile Collection Prototype
     *
     * @var Mage_Launcher_Model_Resource_Tile_Collection
     */
    protected $_tileCollectionBase;

    /**
     * Class constructor
     *
     * @param Mage_Launcher_Model_Resource_Tile_Collection $tileCollectionBase
     * @param Mage_Core_Model_Resource $resource
     */
    public function __construct(
        Mage_Launcher_Model_Resource_Tile_Collection $tileCollectionBase,
        Mage_Core_Model_Resource $resource
    ) {
        parent::__construct($resource);
        $this->_tileCollectionBase = $tileCollectionBase;
    }

    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('launcher_page', 'page_id');
    }

    /**
     * Perform actions after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        parent::_afterLoad($object);

        if ($object->getId()) {
            // Add tiles collection to successfully loaded page (load it lazily) sorted by sort_order
            $tileCollection = clone $this->_tileCollectionBase;
            $tiles = $tileCollection->addFieldToFilter('page_id', array('eq' => $object->getId()))
                ->setOrder('sort_order', Varien_Data_Collection::SORT_ORDER_ASC);
            $object->setTiles($tiles);
        }

        return $this;
    }
}
