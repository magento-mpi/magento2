<?php
/**
 * Magento filesystem memory stream
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Stream_Memory implements Magento_Filesystem_StreamInterface
{
    /**
     * Filesystem object
     *
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Filesystem key
     *
     * @var string
     */
    protected $_key;

    /**
     * Stream mode
     *
     * @var Magento_Filesystem_Stream_Mode
     */
    protected $_mode;

    /**
     * Stream content
     *
     * @var string
     */
    protected $_content;

    /**
     * Size of content in bytes
     *
     * @var int
     */
    protected $_numBytes;

    /**
     * Position of stream pointer
     *
     * @var int
     */
    protected $_position;

    /**
     * Is stream synchronized
     *
     * @var int
     */
    protected $_synchronized;

    /**
     * Constructor
     *
     * @param Magento_Filesystem $filesystem
     * @param string $key
     */
    public function __construct(Magento_Filesystem $filesystem, $key)
    {
        $this->_filesystem = $filesystem;
        $this->_key = $key;
    }

    /**
     * Opens the stream in the specified mode
     *
     * @param Magento_Filesystem_Stream_Mode $mode
     * @throws Magento_Filesystem_Exception
     */
    public function open(Magento_Filesystem_Stream_Mode $mode)
    {
        $this->_mode = $mode;

        $exists = $this->_filesystem->has($this->_key);

        if (($exists && !$mode->allowsExistingFileOpening())
            || (!$exists && !$mode->allowsNewFileOpening())
        ) {
            throw new Magento_Filesystem_Exception('The stream does not allow open.');
        }

        if ($mode->impliesExistingContentDeletion()) {
            $this->_content = $this->_writeContent('');
        } elseif (!$exists && $mode->allowsNewFileOpening()) {
            $this->_content = $this->_writeContent('');
        } else {
            $this->_content = $this->_filesystem->read($this->_key);
        }

        $this->_numBytes = $this->_getSize($this->_content);
        $this->_position = $mode->impliesPositioningCursorAtTheEnd() ? $this->_numBytes : 0;

        $this->_synchronized = true;
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
        if (false === $this->_mode->allowsRead()) {
            throw new LogicException('The stream does not allow read.');
        }

        $chunk = substr($this->_content, $this->_position, $count);
        $this->_position += $this->_getSize($chunk);

        return $chunk;
    }

    /**
     * Reads one CSV row from the stream
     *
     * @param int $count The number of bytes to read
     * @param string $delimiter
     * @param string $enclosure
     * @return array|bool If stream does not allow read.
     * @throws LogicException
     */
    public function readCsv($count = 0, $delimiter = ',', $enclosure = '"')
    {
        // TODO Implement this method
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
        if (false === $this->_mode->allowsWrite()) {
            throw new LogicException('The stream does not allow write.');
        }

        $numWrittenBytes = $this->_getSize($data);

        $newPosition = $this->_position + $numWrittenBytes;
        $newNumBytes = $newPosition > $this->_numBytes ? $newPosition : $this->_numBytes;

        if ($this->eof()) {
            $this->_numBytes += $numWrittenBytes;
            if ($this->_hasNewContentAtFurtherPosition()) {
                $data = str_pad($data, $this->_position + strlen($data), " ", STR_PAD_LEFT);
            }
            $this->_content .= $data;
        } else {
            $before = substr($this->_content, 0, $this->_position);
            $after  = $newNumBytes > $newPosition ? substr($this->_content, $newPosition) : '';
            $this->_content  = $before . $data . $after;
        }

        $this->_position = $newPosition;
        $this->_numBytes = $newNumBytes;
        $this->_synchronized = false;

        return $numWrittenBytes;
    }

    /**
     * Writes one CSV row to the stream.
     *
     * @param array $data
     * @param string $delimiter
     * @param string $enclosure
     * @return integer|bool Returns the length of the written string or FALSE on failure.
     * @throws LogicException If stream does not allow write.
     */
    public function writeCsv(array $data, $delimiter = ',', $enclosure = '"')
    {
        // TODO Implement this method
    }

    /**
     * Closes the stream.
     */
    public function close()
    {
        if (! $this->_synchronized) {
            $this->flush();
        }
    }

    /**
     * Flushes the output.
     */
    public function flush()
    {
        if ($this->_synchronized) {
            return true;
        }

        try {
            $this->_writeContent($this->_content);
        } catch (Exception $e) {
            return false;
        }

        return true;
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
        switch ($whence) {
            case SEEK_SET:
                $this->_position = $offset;
                break;
            case SEEK_CUR:
                $this->_position += $offset;
                break;
            case SEEK_END:
                $this->_position = $this->_numBytes + $offset;
                break;
            default:
                return false;
        }

        return true;
    }

    /**
     * Returns the current position
     *
     * @return int
     */
    public function tell()
    {
        return $this->_position;
    }

    /**
     * Checks if the current position is the end-of-file
     *
     * @return bool
     */
    public function eof()
    {
        return $this->_position >= $this->_numBytes;
    }

    /**
     * @return bool
     */
    protected function _hasNewContentAtFurtherPosition()
    {
        return $this->_position > 0 && !$this->_content;
    }

    /**
     * Writes content to filesystem
     *
     * @param string $content Empty string by default
     * @param bool $overwrite Overwrite by default
     * @return string
     */
    protected function _writeContent($content = '', $overwrite = true)
    {
        $this->_filesystem->write($this->_key, $content, $overwrite);
        return $content;
    }

    /**
     * Gets size of content.
     *
     * @param string $content
     * @return int
     */
    protected function _getSize($content)
    {
        return mb_strlen($content);
    }
}
