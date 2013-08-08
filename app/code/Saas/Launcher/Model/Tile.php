<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Landing page tile model
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Tile extends Mage_Core_Model_Abstract
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
     * @var Saas_Launcher_Model_Tile_StateResolver|null
     */
    protected $_stateResolver;

    /**
     * Save handler associated with current tile
     *
     * @var Saas_Launcher_Model_Tile_SaveHandler|null
     */
    protected $_saveHandler;

    /**
     * @param Mage_Core_Model_Context $context
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param Saas_Launcher_Model_Tile_StateResolver $resolver
     * @param Saas_Launcher_Model_Tile_SaveHandler $handler
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        Saas_Launcher_Model_Tile_StateResolver $resolver = null,
        Saas_Launcher_Model_Tile_SaveHandler $handler = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_init('Saas_Launcher_Model_Resource_Tile');
        $this->_stateResolver = $resolver;
        $this->_saveHandler = $handler;
    }

    /**
     * Load landing page tile by Tile code
     *
     * @param string $tileCode
     * @return Saas_Launcher_Model_Tile
     */
    public function loadByTileCode($tileCode)
    {
        return $this->load($tileCode, 'tile_code');
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
     * @param Saas_Launcher_Model_Tile_StateResolver|null $stateResolver
     * @return Saas_Launcher_Model_Tile
     */
    public function setStateResolver(Saas_Launcher_Model_Tile_StateResolver $stateResolver)
    {
        $this->_stateResolver = $stateResolver;
        return $this;
    }

    /**
     * Retrieve state resolver associated with current tile
     *
     * @return Saas_Launcher_Model_Tile_StateResolver|null
     */
    public function getStateResolver()
    {
        return $this->_stateResolver;
    }

    /**
     * Set save handler associated with the current tile
     *
     * @param Saas_Launcher_Model_Tile_SaveHandler|null $saveHandler
     * @return Saas_Launcher_Model_Tile
     */
    public function setSaveHandler(Saas_Launcher_Model_Tile_SaveHandler $saveHandler)
    {
        $this->_saveHandler = $saveHandler;
        return $this;
    }

    /**
     * Retrieve save handler associated with the current tile
     *
     * @return Saas_Launcher_Model_Tile_SaveHandler|null
     */
    public function getSaveHandler()
    {
        return $this->_saveHandler;
    }

    /**
     * Refresh Tile State
     *
     * @param array $data
     * @return bool
     */
    public function refreshState(array $data = array())
    {
        if (!empty($data)) {
            $this->getSaveHandler()->save($data);
        }
        $freshState = $this->getStateResolver()->getPersistentState();
        $this->setState($freshState);
        $this->save();
    }
}

