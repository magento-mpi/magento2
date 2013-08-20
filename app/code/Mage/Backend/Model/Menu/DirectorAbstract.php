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
     * Factory model
     * @var Mage_Backend_Model_Menu_Builder_CommandFactory
     */
    protected $_commandFactory;

    /**
     * @param Mage_Backend_Model_Menu_Builder_CommandFactory $factory
     */
    public function __construct(Mage_Backend_Model_Menu_Builder_CommandFactory $factory)
    {
        $this->_commandFactory = $factory;
    }

    /**
     * Build menu instance
     *
     * @param array $config
     * @param Mage_Backend_Model_Menu_Builder $builder
     * @param Mage_Core_Model_Logger $logger
     */
    abstract public function direct(
        array $config, Mage_Backend_Model_Menu_Builder $builder, Mage_Core_Model_Logger $logger
    );
}
