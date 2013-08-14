<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Landing page resource model
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Resource_Page extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Tile Collection Prototype
     *
     * @var Saas_Launcher_Model_Resource_Tile_Collection
     */
    protected $_tileCollectionBase;

    /**
     * Class constructor
     *
     * @param Saas_Launcher_Model_Resource_Tile_Collection $tileCollectionBase
     * @param Magento_Core_Model_Resource $resource
     */
    public function __construct(
        Saas_Launcher_Model_Resource_Tile_Collection $tileCollectionBase,
        Magento_Core_Model_Resource $resource
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
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        parent::_afterLoad($object);

        if ($object->getId()) {
            // Add tiles collection to successfully loaded page (load it lazily) sorted by sort_order
            $tileCollection = clone $this->_tileCollectionBase;
            $tiles = $tileCollection->addFieldToFilter('page_code', array('eq' => $object->getPageCode()))
                ->setOrder('sort_order', Magento_Data_Collection::SORT_ORDER_ASC);
            $object->setTiles($tiles);
        }

        return $this;
    }
}
