<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\Migration\System\Writer;

class Memory implements \Magento\Tools\Migration\System\WriterInterface
{
    /**
     * @param string $fileName
     * @param string $contents
     * @return void
     */
    public function write($fileName, $contents)
    {
    }

    /**
     * Remove file
     *
     * @param string $fileName
     * @return void
     */
    public function remove($fileName)
    {
    }
}
