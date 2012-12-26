<?php
/**
 * Magento filesystem local stream
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Stream_Local implements Magento_Filesystem_StreamInterface
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Stream path
     *
     * @var string
     */
    protected $_path;

    /**
     * Stream mode
     *
     * @var Magento_Filesystem_Stream_Mode
     */
    protected $_mode;

    /**
     * Stream file resource handle
     *
     * @var
     */
    protected $_fileHandle;

    /**
     * Constructor
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->_path = $path;
    }

    /**
     * Opens the stream in the specified mode
     *
     * @param Magento_Filesystem_Stream_Mode $mode
     * @return bool
     * @throws RuntimeException If stream cannot be opened
     */
    public function open(Magento_Filesystem_Stream_Mode $mode)
    {
        $fileHandle = @fopen($this->_path, $mode->getMode());

        if (false === $fileHandle) {
            throw new RuntimeException(sprintf('File "%s" cannot be opened', $this->_path));
        }

        $this->_mode = $mode;
        $this->_fileHandle = $fileHandle;

        return true;
    }

    /**
     * Reads the specified number of bytes from the current position.
     *
     * @param integer $count The number of bytes to read
     * @return string
     * @throws LogicException If stream does not allow read.
     */
    public function read($count)
    {
        if (! $this->_fileHandle) {
            return false;
        }

        if (false === $this->_mode->allowsRead()) {
            throw new LogicException('The stream does not allow read.');
        }

        return fread($this->_fileHandle, $count);
    }

    /**
     * Writes the data to stream.
     *
     * @param string $data
     * @return integer The number of bytes that were successfully written
     * @throws LogicException If stream does not allow write.
     */
    public function write($data)
    {
        if (! $this->_fileHandle) {
            return false;
        }

        if (false === $this->_mode->allowsWrite()) {
            throw new LogicException('The stream does not allow write.');
        }

        return fwrite($this->_fileHandle, $data);
    }

    /**
     * Closes the stream.
     */
    public function close()
    {
        if (! $this->_fileHandle) {
            return false;
        }

        $closed = fclose($this->_fileHandle);

        if ($closed) {
            $this->_mode = null;
            $this->_fileHandle = null;
        }

        return $closed;
    }

    /**
     * Flushes the output.
     */
    public function flush()
    {
        if ($this->_fileHandle) {
            return fflush($this->_fileHandle);
        }

        return false;
    }

    /**
     * Seeks to the specified offset
     *
     * @param int $offset
     * @param int $whence
     * @return bool
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if ($this->_fileHandle) {
            return 0 === fseek($this->_fileHandle, $offset, $whence);
        }

        return false;
    }

    /**
     * Returns the current position
     *
     * @return int
     */
    public function tell()
    {
        if ($this->_fileHandle) {
            return ftell($this->_fileHandle);
        }

        return false;
    }

    /**
     * Checks if the current position is the end-of-file
     *
     * @return bool
     */
    public function eof()
    {
        if ($this->_fileHandle) {
            return feof($this->_fileHandle);
        }

        return true;
    }

    /**
     * Delete a file
     *
     * @return bool
     */
    public function unlink()
    {
        if ($this->_mode && $this->_mode->impliesExistingContentDeletion()) {
            return @unlink($this->_path);
        }

        return false;
    }
}
