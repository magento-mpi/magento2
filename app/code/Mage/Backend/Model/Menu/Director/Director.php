<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Director_Director extends Mage_Backend_Model_Menu_DirectorAbstract
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
     * @param Mage_Core_Model_Logger $logger
     * @return Mage_Backend_Model_Menu_Builder_CommandAbstract
     */
    protected function _getCommand($data, $logger)
    {
        $command = $this->_commandFactory->create($data['type'], array('data' => $data));
        if (isset($this->_messagePatterns[$data['type']])) {
            $logger->logDebug(sprintf($this->_messagePatterns[$data['type']], $command->getId()),
                Mage_Backend_Model_Menu::LOGGER_KEY
            );
        }
        return $command;
    }

    /**
     * Build menu instance
     *
     * @param array $config
     * @param Mage_Backend_Model_Menu_Builder $builder
     * @param Mage_Core_Model_Logger $logger
     */
    public function direct(
        array $config,
        Mage_Backend_Model_Menu_Builder $builder,
        Mage_Core_Model_Logger $logger
    ) {
        foreach ($config as $data) {
            $builder->processCommand($this->_getCommand($data, $logger));
        }
    }
}
