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
     * Factory model
     * @var Magento_Backend_Model_Menu_Builder_CommandFactory
     */
    protected $_commandFactory;

    /**
     * @param Magento_Backend_Model_Menu_Builder_CommandFactory $factory
     */
    public function __construct(Magento_Backend_Model_Menu_Builder_CommandFactory $factory)
    {
        $this->_commandFactory = $factory;
    }

    /**
     * Build menu instance
     *
     * @param array $config
     * @param Magento_Backend_Model_Menu_Builder $builder
     * @param Magento_Core_Model_Logger $logger
     */
    abstract public function direct(
        array $config, Magento_Backend_Model_Menu_Builder $builder, Magento_Core_Model_Logger $logger
    );
}
