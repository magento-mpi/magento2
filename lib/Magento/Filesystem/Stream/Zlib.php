<?php
/**
 * Magento filesystem zlib local stream
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Stream_Zlib extends Magento_Filesystem_Stream_Local
{
    /**
     * Opens the stream in the specified mode
     *
     * @param Magento_Filesystem_Stream_Mode|string $mode
     */
    public function open($mode)
    {
        if ($mode instanceof Magento_Filesystem_Stream_Mode) {
            $mode = $mode->getMode();
        }
        $mode = new Magento_Filesystem_Stream_Mode_Zlib($mode);
        parent::open($mode);
    }
}
