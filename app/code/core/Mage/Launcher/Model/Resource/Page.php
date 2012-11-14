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
    protected $_tileCollectionPrototype;

    /**
     * Class constructor
     *
     * @param Mage_Launcher_Model_Resource_Tile_Collection $tileCollectionPrototype
     * @param Mage_Core_Model_Resource $resource
     */
    public function __construct(
        Mage_Launcher_Model_Resource_Tile_Collection $tileCollectionPrototype,
        Mage_Core_Model_Resource $resource
    ) {
        parent::__construct($resource);
        $this->_tileCollectionPrototype = $tileCollectionPrototype;
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
        // Add tiles collection to page (load it lazily)
        $tileCollection = clone $this->_tileCollectionPrototype;
        $tiles = $tileCollection->addFieldToFilter('page_id', array('eq' => $object->getId()));
        $object->setTiles($tiles);
        return $this;
    }

    /**
     * Load landing page by its code
     *
     * @param Mage_Launcher_Model_Page $page
     * @param string $code
     * @return Mage_Launcher_Model_Resource_Page
     */
    public function loadByCode($page, $code)
    {
        return $this->load($page, $code, 'code');
    }
}
