<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Tools_Migration_System_Configuration_Logger_Factory
{
    /**
     * @param string $loggerType
     * @param string $filePath
     * @param Tools_Migration_System_FileManager $fileManager
     * @return Tools_Migration_System_Configuration_LoggerAbstract
     */
    public function getLogger($loggerType, $filePath, Tools_Migration_System_FileManager $fileManager)
    {
        $loggerClassName = null;
        switch ($loggerType) {
            case 'file':
                $loggerClassName = 'Tools_Migration_System_Configuration_Logger_File';
                break;
            default:
                $loggerClassName = 'Tools_Migration_System_Configuration_Logger_Console';
                break;
        }

        return new $loggerClassName($filePath, $fileManager);
    }
}
