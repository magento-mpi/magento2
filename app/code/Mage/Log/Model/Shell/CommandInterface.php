<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Log_Model_Shell_CommandInterface
{
    /**
     * Execute command
     *
     * @return string
     */
    public function execute();
}