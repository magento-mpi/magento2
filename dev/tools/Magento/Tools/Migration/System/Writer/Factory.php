<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Migration\System\Writer;

class Factory
{
    /**
     * @param string $type
     * @return \Magento\Tools\Migration\System\WriterInterface
     */
    public function getWriter($type)
    {
        $writerClassName = null;
        switch ($type) {
            case 'write':
                $writerClassName = '\Magento\Tools\Migration\System\Writer\FileSystem';
                break;
            default:
                $writerClassName = '\Magento\Tools\Migration\System\Writer\Memory';
                break;
        }
        return new $writerClassName();
    }
}
