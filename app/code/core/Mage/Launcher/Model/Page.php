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
 * Landing page model
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Page extends Mage_Core_Model_Abstract
{
    /**
     * Prefix for model event names
     *
     * @var string
     */
    protected $_eventPrefix = 'launcher_page';

    /**
     * List of tiles associated with page
     *
     * @var Mage_Launcher_Model_Resource_Tile_Collection|null
     */
    protected $_tiles;

    /**
     * Class constructor
     *
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_Cache $cacheManager
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($eventDispatcher, $cacheManager, $resource, $resourceCollection, $data);
        $this->_init('Mage_Launcher_Model_Resource_Page');
    }

    /**
     * Retrieve collection of tiles associated with this page
     *
     * @return Mage_Launcher_Model_Resource_Tile_Collection|null
     */
    public function getTiles()
    {
        return $this->_tiles;
    }

    /**
     * Set collection of related tiles
     *
     * @param Mage_Launcher_Model_Resource_Tile_Collection|null $tiles
     * @return Mage_Launcher_Model_Page
     */
    public function setTiles(Mage_Launcher_Model_Resource_Tile_Collection $tiles)
    {
        $this->_tiles = $tiles;
        return $this;
    }

    /**
     * Load landing page by its code
     *
     * @param $code
     * @return Mage_Launcher_Model_Page
     */
    public function loadByPageCode($code)
    {
        return $this->load($code, 'page_code');
    }

    /**
     * Check if page is complete (i.e. all related tiles are complete)
     *
     * @return bool
     */
    public function isComplete()
    {
        $isComplete = true;
        foreach ($this->getTiles() as $tile) {
            if (!$tile->isComplete()) {
                $isComplete = false;
                break;
            }
        }
        return $isComplete;
    }
}
