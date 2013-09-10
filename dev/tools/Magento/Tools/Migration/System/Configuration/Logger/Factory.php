<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Migration\System\Configuration\Logger;

class Factory
{
    /**
     * Get logger instance
     *
     * @param string $loggerType
     * @param string $filePath
     * @param \Magento\Tools\Migration\System\FileManager $fileManager
     * @return \Magento\Tools\Migration\System\Configuration\LoggerAbstract
     */
    public function getLogger($loggerType, $filePath, \Magento\Tools\Migration\System\FileManager $fileManager)
    {
        /** @var \Magento\Tools\Migration\System\Configuration\LoggerAbstract $loggerInstance  */
        $loggerInstance = null;
        switch ($loggerType) {
            case 'file':
                $loggerInstance = new \Magento\Tools\Migration\System\Configuration\Logger\File($filePath, $fileManager);
                break;
            default:
                $loggerInstance = new \Magento\Tools\Migration\System\Configuration\Logger\Console();
                break;
        }

        return $loggerInstance;
    }
}
