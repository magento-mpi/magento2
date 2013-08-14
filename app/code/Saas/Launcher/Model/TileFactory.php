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
 * Landing page tile factory
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_TileFactory
{
    /**
     * Pattern of XML path to <state_resolver> node
     */
    const XML_PATH_TILE_STATE_RESOLVER_PATTERN = 'adminhtml/launcher/pages/%s/tiles/%s/state_resolver';

    /**
     * Pattern of XML path to <save_handler> node
     */
    const XML_PATH_TILE_SAVE_HANDLER_PATTERN = 'adminhtml/launcher/pages/%s/tiles/%s/save_handler';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_applicationConfig;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Config $applicationConfig
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_Config $applicationConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_applicationConfig = $applicationConfig;
    }

    /**
     * Create new tile model
     *
     * @param string $tileCode
     * @param array $arguments
     * @return Saas_Launcher_Model_Tile
     * @throws Saas_Launcher_Exception
     */
    public function create($tileCode = null, array $arguments = array())
    {
        $tile = $this->_objectManager->create('Saas_Launcher_Model_Tile', $arguments);
        if (isset($tileCode)) {
            $tile->loadByTileCode($tileCode);
            if ($tile->getId()) {
                $this->setStateResolverAndSaveHandler($tile);
            } else {
                throw new Saas_Launcher_Exception('Tile is not defined for specified tile code: "' . $tileCode . '".');
            }
        }
        return $tile;
    }

    /**
     * Retrieve class name of state resolver associated with tile that has given page and tile codes
     *
     * @param string $pageCode
     * @param string $tileCode
     * @return string
     * @throws Saas_Launcher_Exception
     */
    public function getStateResolverClassName($pageCode, $tileCode)
    {
        return $this->_getClassName(
            'State Resolver',
            $pageCode,
            $tileCode,
            self::XML_PATH_TILE_STATE_RESOLVER_PATTERN
        );
    }

    /**
     * Retrieve class name of save handler associated with tile that has given page and tile codes
     *
     * @param string $pageCode
     * @param string $tileCode
     * @return string
     * @throws Saas_Launcher_Exception
     */
    public function getSaveHandlerClassName($pageCode, $tileCode)
    {
        return $this->_getClassName(
            'Save Handler',
            $pageCode,
            $tileCode,
            self::XML_PATH_TILE_SAVE_HANDLER_PATTERN
        );
    }

    /**
     * Retrieve class name by given page and tile codes and section path
     *
     * @param string $entity name of the entity whose class is retrieved
     * @param string $pageCode
     * @param string $tileCode
     * @param string $sectionPath
     * @return string
     * @throws Saas_Launcher_Exception
     */
    protected function _getClassName($entity, $pageCode, $tileCode, $sectionPath)
    {
        $className = (string)$this->_applicationConfig->getNode(sprintf($sectionPath, $pageCode, $tileCode));

        if (empty($className)) {
            throw new Saas_Launcher_Exception($entity . ' is not defined for tile with code "' . $tileCode . '".');
        }

        return $className;
    }

    /**
     * Add corresponding state resolver and save handler to successfully loaded tile
     *
     * @param Saas_Launcher_Model_Tile $tile
     */
    public function setStateResolverAndSaveHandler(Saas_Launcher_Model_Tile $tile)
    {
        $pageCode = $tile->getPageCode();
        $tileCode = $tile->getTileCode();

        $resolverClassName = $this->getStateResolverClassName($pageCode, $tileCode);
        $stateResolver = $this->_objectManager->create($resolverClassName, array());
        $tile->setStateResolver($stateResolver);

        $handlerClassName = $this->getSaveHandlerClassName($pageCode, $tileCode);
        $saveHandler = $this->_objectManager->create($handlerClassName, array());
        $tile->setSaveHandler($saveHandler);
    }
}
