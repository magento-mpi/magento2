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
 * Landing page tile model
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Tile extends Mage_Core_Model_Abstract
{
    /**
     * Possible tile states
     */
    const STATE_TODO = 0;
    const STATE_COMPLETE = 1;
    const STATE_DISMISSED = 2;
    const STATE_SKIPPED = 3;

    /**
     * Prefix for model event names
     *
     * @var string
     */
    protected $_eventPrefix = 'launcher_tile';

    /**
     * State resolver associated with current tile
     *
     * @var Mage_Launcher_Model_Tile_StateResolver|null
     */
    protected $_stateResolver;

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
        $this->_init('Mage_Launcher_Model_Resource_Tile');
    }

    /**
     * Load landing page tile by its code
     *
     * @param $code
     * @return Mage_Launcher_Model_Tile
     */
    public function loadByCode($code)
    {
        $this->_getResource()->loadByCode($this, $code);
        return $this;
    }

    /**
     * Check if tile can be skipped
     *
     * @return bool
     */
    public function isSkippable()
    {
        return (bool)$this->getData('is_skippable');
    }

    /**
     * Check if tile can be dismissed
     *
     * @return bool
     */
    public function isDismissible()
    {
        return (bool)$this->getData('is_dismissible');
    }

    /**
     * Check if the tile is complete
     *
     * @return bool
     */
    public function isComplete()
    {
        return $this->getState() == self::STATE_COMPLETE;
    }

    /**
     * Set state resolver associated with current tile
     *
     * @param Mage_Launcher_Model_Tile_StateResolver|null $stateResolver
     * @return Mage_Launcher_Model_Tile
     */
    public function setStateResolver(Mage_Launcher_Model_Tile_StateResolver $stateResolver)
    {
        $this->_stateResolver = $stateResolver;
        return $this;
    }

    /**
     * Retrieve state resolver associated with current tile
     *
     * @return Mage_Launcher_Model_Tile_StateResolver|null
     */
    public function getStateResolver()
    {
        return $this->_stateResolver;
    }
}
