<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Magento_Backend_Model_Menu_DirectorAbstract
{
    /**
     * Configuration data
     * @var
     */
    protected $_configModel;

    /**
     * Factory model
     * @var Magento_ObjectManager
     */
    protected $_factory;

    /**
     * @param $menuConfig
     * @param Magento_ObjectManager $factory
     */
    public function __construct(
        $menuConfig,
        Magento_ObjectManager $factory
    ) {
        $this->_configModel = $menuConfig;
        $this->_factory = $factory;
    }

    /**
     * Apply menu commands to builder object
     * @abstract
     * @param  Magento_Backend_Model_Menu_Builder $builder
     * @return Magento_Backend_Model_Menu_DirectorAbstract
     */
    abstract public function buildMenu(Magento_Backend_Model_Menu_Builder $builder);
}
