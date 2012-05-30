<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Backend_Model_Menu_Builder_DirectorAbstract
{
    /**
     * Configuration data
     * @var
     */
    protected $_configModel;

    /**
     * Factory model
     * @var Mage_Core_Model_Config
     */
    protected $_factory;

    /**
     * @param array $data
     * @throws InvalidArgumentException if config storage is not present in $data array
     */
    public function __construct(array $data = array())
    {
        if (isset($data['config'])) {
            $this->_configModel = $data['config'];
        } else {
            throw new InvalidArgumentException('Configuration storage model is required parameter');
        }

        if (isset($data['factory'])) {//} && $data['factory'] instanceof Mage_Core_Model_Config) {
            $this->_factory = $data['factory'];
        } else {
            throw new InvalidArgumentException('Configuration factory model is required parameter');
        }
    }

    /**
     * Apply menu commands to builder object
     * @abstract
     * @param  Mage_Backend_Model_Menu_BuilderAbstract $builder
     * @return Mage_Backend_Model_Menu_Builder_DirectorAbstract
     */
    abstract public function buildMenu($builder);
}
