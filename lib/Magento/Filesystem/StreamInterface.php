<?php
/**
 * Interface of Magento filesystem stream
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Filesystem_StreamInterface
{
    /**
     * Opens the stream in the specified mode
     *
     * @param Magento_Filesystem_Stream_Mode $mode
     * @return bool
     * @throws RuntimeException If stream cannot be opened
     */
    public function open(Magento_Filesystem_Stream_Mode $mode);

    /**
     * Reads the specified number of bytes from the current position.
     *
     * @param integer $count The number of bytes to read
     * @return string
     * @throws LogicException If stream does not allow read.
     */
    public function read($count);

    /**
     * Writes the data to stream.
     *
     * @param string $data
     * @return integer The number of bytes that were successfully written
     * @throws LogicException If stream does not allow write.
     */
    public function write($data);

    /**
     * Closes the stream.
     */
    public function close();

    /**
     * Flushes the output.
     */
    public function flush();

    /**
     * Seeks to the specified offset
     *
     * @param int $offset
     * @param int $whence
     * @return bool
     */
    public function seek($offset, $whence = SEEK_SET);

    /**
     * Returns the current position
     *
     * @return int
     */
    public function tell();

    /**
     * Checks if the current position is the end-of-file
     *
     * @return bool
     */
    public function eof();

    /**
     * Delete a file
     *
     * @return bool
     */
    public function unlink();
}
