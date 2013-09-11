<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Menu\Director;

class Director extends \Magento\Backend\Model\Menu\DirectorAbstract
{
    /**
     * Log message patterns
     *
     * @var array
     */
    protected $_messagePatterns = array(
        'update' => 'Item %s was updated',
        'remove' => 'Item %s was removed'
    );

    /**
     * Get command object
     *
     * @param array $data command params
     * @param \Magento\Core\Model\Logger $logger
     * @return \Magento\Backend\Model\Menu\Builder\CommandAbstract
     */
    protected function _getCommand($data, $logger)
    {
        $command = $this->_commandFactory->create($data['type'], array('data' => $data));
        if (isset($this->_messagePatterns[$data['type']])) {
            $logger->logDebug(sprintf($this->_messagePatterns[$data['type']], $command->getId()),
                \Magento\Backend\Model\Menu::LOGGER_KEY
            );
        }
        return $command;
    }

    /**
     * Build menu instance
     *
     * @param array $config
     * @param \Magento\Backend\Model\Menu\Builder $builder
     * @param \Magento\Core\Model\Logger $logger
     */
    public function direct(
        array $config,
        \Magento\Backend\Model\Menu\Builder $builder,
        \Magento\Core\Model\Logger $logger
    ) {
        foreach ($config as $data) {
            $builder->processCommand($this->_getCommand($data, $logger));
        }
    }
}
