<?php
/**
 * Application config loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader implements Mage_Core_Model_Config_LoaderInterface
{
    /**
     * Configuration loader factory
     *
     * @var Mage_Core_Model_Config_LoaderFactory
     */
    protected $_loaderFactory;

    /**
     * Loader names
     *
     * @var array
     */
    protected $_loaders;

    /**
     * @param Mage_Core_Model_Config_LoaderFactory $loaderFactory
     * @param array $loaders
     */
    public function __construct(
        Mage_Core_Model_Config_LoaderFactory $loaderFactory,
        array $loaders
    ) {
        $this->_loaderFactory = $loaderFactory;
        $this->_loaders = $loaders;
    }

    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config)
    {
        foreach ($this->_loaders as $loaderName) {
            $this->_loaderFactory->create($loaderName)->load($config);
        }
    }
}
