<?php
/**
 * Magento filesystem zlib local stream
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Stream;

class Zlib extends \Magento\Filesystem\Stream\Local
{
    /**
     * Opens the stream in the specified mode
     *
     * @param \Magento\Filesystem\Stream\Mode|string $mode
     */
    public function open($mode)
    {
        if ($mode instanceof \Magento\Filesystem\Stream\Mode) {
            $mode = $mode->getMode();
        }
        $mode = new \Magento\Filesystem\Stream\Mode\Zlib($mode);
        parent::open($mode);
    }
}
