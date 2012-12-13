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
 * Landing page tile resource model
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Resource_Tile extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * State resolver factory
     *
     * @var Mage_Launcher_Model_Tile_StateResolverFactory
     */
    protected $_resolverFactory;

    /**
     * Save handler factory
     *
     * @var Mage_Launcher_Model_Tile_SaveHandlerFactory
     */
    protected $_saveHandlerFactory;

    /**
     * Class constructor
     *
     * @param Mage_Launcher_Model_Tile_StateResolverFactory $stateResolverFactory
     * @param Mage_Launcher_Model_Tile_SaveHandlerFactory $saveHandlerFactory
     * @param Mage_Core_Model_Resource $resource
     */
    public function __construct(
        Mage_Launcher_Model_Tile_StateResolverFactory $stateResolverFactory,
        Mage_Launcher_Model_Tile_SaveHandlerFactory $saveHandlerFactory,
        Mage_Core_Model_Resource $resource
    ) {
        parent::__construct($resource);
        $this->_resolverFactory = $stateResolverFactory;
        $this->_saveHandlerFactory = $saveHandlerFactory;
    }

    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('launcher_tile', 'tile_id');
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
            // Add corresponding state resolver and save handler to successfully loaded tile
            $stateResolver = $this->_resolverFactory->create($object->getCode());
            $object->setStateResolver($stateResolver);

            $saveHandler = $this->_saveHandlerFactory->create($object->getCode());
            $object->setSaveHandler($saveHandler);
        }

        return $this;
    }
}
