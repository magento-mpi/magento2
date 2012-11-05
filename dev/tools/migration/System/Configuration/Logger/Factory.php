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
     * Get logger instance
     *
     * @param string $loggerType
     * @param string $filePath
     * @param Tools_Migration_System_FileManager $fileManager
     * @return Tools_Migration_System_Configuration_LoggerAbstract
     */
    public function getLogger($loggerType, $filePath, Tools_Migration_System_FileManager $fileManager)
    {
        /** @var Tools_Migration_System_Configuration_LoggerAbstract $loggerInstance  */
        $loggerInstance = null;
        switch ($loggerType) {
            case 'file':
                $loggerInstance = new Tools_Migration_System_Configuration_Logger_File($filePath, $fileManager);
                break;
            default:
                $loggerInstance = new Tools_Migration_System_Configuration_Logger_Console();
                break;
        }

        return $loggerInstance;
    }
}
