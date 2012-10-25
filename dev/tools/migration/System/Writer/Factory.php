<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once __DIR__ . '/FileSystem.php';
require_once __DIR__ . '/Memory.php';
class Tools_Migration_System_Writer_Factory
{
    /**
     * @param string $type
     * @return Tools_Migration_System_WriterInterface
     */
    public function getWriter($type)
    {
        $writerClassName = null;
        switch ($type) {
            case 'write':
                $writerClassName = 'Tools_Migration_System_Writer_FileSystem';
                break;
            default:
                $writerClassName = 'Tools_Migration_System_Writer_Memory';
                break;
        }
        return new $writerClassName();
    }
}
