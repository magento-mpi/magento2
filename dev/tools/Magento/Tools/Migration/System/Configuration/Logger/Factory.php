<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Tools_Migration_System_Configuration_Logger_Factory
{
    /**
     * Get logger instance
     *
     * @param string $loggerType
     * @param string $filePath
     * @param Magento_Tools_Migration_System_FileManager $fileManager
     * @return Magento_Tools_Migration_System_Configuration_LoggerAbstract
     */
    public function getLogger($loggerType, $filePath, Magento_Tools_Migration_System_FileManager $fileManager)
    {
        /** @var Magento_Tools_Migration_System_Configuration_LoggerAbstract $loggerInstance  */
        $loggerInstance = null;
        switch ($loggerType) {
            case 'file':
                $loggerInstance = new Magento_Tools_Migration_System_Configuration_Logger_File($filePath, $fileManager);
                break;
            default:
                $loggerInstance = new Magento_Tools_Migration_System_Configuration_Logger_Console();
                break;
        }

        return $loggerInstance;
    }
}
