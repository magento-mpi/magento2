<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Tools_Migration_System_Writer_Factory
{
    /**
     * @param string $type
     * @return Magento_Tools_Migration_System_WriterInterface
     */
    public function getWriter($type)
    {
        $writerClassName = null;
        switch ($type) {
            case 'write':
                $writerClassName = 'Magento_Tools_Migration_System_Writer_FileSystem';
                break;
            default:
                $writerClassName = 'Magento_Tools_Migration_System_Writer_Memory';
                break;
        }
        return new $writerClassName();
    }
}
