<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

/**
 * Shell command line wrapper encapsulates command execution and arguments escaping
 */
interface ShellInterface
{
    /**
     * Execute a command through the command line, passing properly escaped arguments
     *
     * @param string $command Command with optional argument markers '%s'
     * @param string[] $arguments Argument values to substitute markers with
     * @throws \Magento\Exception If a command returns non-zero exit code
     * @return void|string
     */
    public function execute($command, array $arguments = array());
}
