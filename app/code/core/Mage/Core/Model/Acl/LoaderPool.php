<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_Acl_LoaderPool implements IteratorAggregate
{
    /**
     * Application config
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Mage_Core_Model_Config $config, Magento_ObjectManager $objectManager)
    {
        $this->_config = $config;
        $this->_objectManager = $objectManager;
    }

    /**
     * Retrieve ACL loader class from config or NullLoader if not defined
     *
     * @param string $loaderType
     * @return string
     * @throws LogicException
     */
    protected function _getLoaderClass($loaderType)
    {
        $areaConfig = $this->_config->getAreaConfig(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        if (!isset($areaConfig['acl'])) {
            throw new LogicException('No acl configuration is specified');
        }

        return (string) (isset($areaConfig['acl'][$loaderType . 'Loader'])
            ? $areaConfig['acl'][$loaderType . 'Loader']
            : 'Magento_Acl_Loader_Default');
    }

    /**
     * @return ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator(array(
            $this->_objectManager->get($this->_getLoaderClass('resource')),
            $this->_objectManager->get($this->_getLoaderClass('role')),
            $this->_objectManager->get($this->_getLoaderClass('rule')),
        ));
    }
}
