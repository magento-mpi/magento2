<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model\Shell\Command;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create clean command
     *
     * @param int $days
     * @return \Magento\Log\Model\Shell\CommandInterface
     */
    public function createCleanCommand($days)
    {
        return $this->_objectManager->create('Magento\Log\Model\Shell\Command\Clean', array('days' => $days));
    }

    /**
     * Create status command
     *
     * @return \Magento\Log\Model\Shell\CommandInterface
     */
    public function createStatusCommand()
    {
        return $this->_objectManager->create('Magento\Log\Model\Shell\Command\Status');
    }
}
