<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Migration\System;

interface WriterInterface
{
    /**
     * @param string $fileName
     * @param string $contents
     * @return void
     */
    public function write($fileName, $contents);

    /**
     * Remove file
     *
     * @param string $fileName
     * @return void
     */
    public function remove($fileName);
}
