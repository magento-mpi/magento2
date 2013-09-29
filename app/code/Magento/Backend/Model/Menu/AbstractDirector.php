<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Menu;

abstract class AbstractDirector
{
    /**
     * Factory model
     * @var \Magento\Backend\Model\Menu\Builder\CommandFactory
     */
    protected $_commandFactory;

    /**
     * @param \Magento\Backend\Model\Menu\Builder\CommandFactory $factory
     */
    public function __construct(\Magento\Backend\Model\Menu\Builder\CommandFactory $factory)
    {
        $this->_commandFactory = $factory;
    }

    /**
     * Build menu instance
     *
     * @param array $config
     * @param \Magento\Backend\Model\Menu\Builder $builder
     * @param \Magento\Core\Model\Logger $logger
     */
    abstract public function direct(
        array $config, \Magento\Backend\Model\Menu\Builder $builder, \Magento\Core\Model\Logger $logger
    );
}
