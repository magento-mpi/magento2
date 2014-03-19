<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Menu\Builder;

/**
 * Menu builder command factory
 */
class CommandFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new command object
     *
     * @param string $commandName
     * @param array $data
     * @return \Magento\Backend\Model\Config
     */
    public function create($commandName, array $data = array())
    {
        return $this->_objectManager->create(
            'Magento\Backend\Model\Menu\Builder\Command\\' . ucfirst($commandName),
            $data
        );
    }
}
