<?php
/**
 * Magento filesystem stream mode
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Stream_Mode
{
    /**
     * A stream mode as for the use of fopen()
     *
     * @var string
     */
    protected $_mode;

    /**
     * Base mode (e.g "r", "w", "a")
     *
     * @var string
     */
    protected $_base;

    /**
     * Is mode has plus (e.g. "w+")
     *
     * @var string
     */
    protected $_plus;

    /**
     * Additional mode of stream (e.g. "rb")
     *
     * @var string
     */
    protected $_flag;

    /**
     * Constructor
     *
     * @param string $mode
     */
    public function __construct($mode)
    {
        $this->_mode = $mode;

        $mode = substr($mode, 0, 3);
        $rest = substr($mode, 1);

        $this->_base = substr($mode, 0, 1);
        $this->_plus = false !== strpos($rest, '+');
        $this->_flag = trim($rest, '+');
    }

    /**
     * Returns the underlying mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * Indicates whether the mode allows to read
     *
     * @return bool
     */
    public function allowsRead()
    {
        if ($this->_plus) {
            return true;
        }

        return 'r' === $this->_base;
    }

    /**
     * Checks whether the mode allows to write.
     *
     * @return bool
     */
    public function allowsWrite()
    {
        if ($this->_plus) {
            return true;
        }

        return 'r' !== $this->_base;
    }

    /**
     * Checks whether the mode allows to open an existing file.
     *
     * @return bool
     */
    public function allowsExistingFileOpening()
    {
        return 'x' !== $this->_base;
    }

    /**
     * Checks whether the mode allows to create a new file.
     *
     * @return bool
     */
    public function allowsNewFileOpening()
    {
        return 'r' !== $this->_base;
    }

    /**
     * Indicates whether the mode implies to delete the existing content of the file when it already exists
     *
     * @return bool
     */
    public function impliesExistingContentDeletion()
    {
        return 'w' === $this->_base;
    }

    /**
     * Indicates whether the mode implies positioning the cursor at the beginning of the file
     *
     * @return bool
     */
    public function impliesPositioningCursorAtTheBeginning()
    {
        return 'a' !== $this->_base;
    }

    /**
     * Indicates whether the mode implies positioning the cursor at the end of the file
     *
     * @return bool
     */
    public function impliesPositioningCursorAtTheEnd()
    {
        return 'a' === $this->_base;
    }

    /**
     * Indicates whether the stream is in binary mode
     *
     * @return bool
     */
    public function isBinary()
    {
        return 'b' === $this->_flag;
    }
}
