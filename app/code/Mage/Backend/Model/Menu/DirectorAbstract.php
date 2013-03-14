<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Backend_Model_Menu_DirectorAbstract
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
     * @param  Mage_Backend_Model_Menu_Builder $builder
     * @return Mage_Backend_Model_Menu_DirectorAbstract
     */
    abstract public function buildMenu(Mage_Backend_Model_Menu_Builder $builder);
}
