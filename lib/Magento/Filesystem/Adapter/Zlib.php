<?php
/**
 * Adapter for local compressed filesystem
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Adapter;

class Zlib extends \Magento\Filesystem\Adapter\Local
{
    /**
     * @var int
     */
    protected $_compressRatio;

    /**
     * @var string
     */
    protected $_strategy;

    /**
     * @var null|bool
     */
    protected $_hasCompression = null;

    /**
     * Initialize Zlib adapter.
     *
     * @param int $ratio
     * @param string $strategy
     */
    public function __construct($ratio = 1, $strategy = '')
    {
        $this->_compressRatio = $ratio;
        $this->_strategy = $strategy;
    }

    /**
     * Read compressed file file
     *
     * @param string $key
     * @return string
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function read($key)
    {
        $stream = $this->createStream($key);
        $stream->open('rb');

        $info = unpack("lcompress/llength", $stream->read(8));

        $compressed = (bool)$info['compress'];
        if ($compressed && !$this->_isCompressionAvailable()) {
            $stream->close();
            throw new \Magento\Filesystem\FilesystemException(
                'The file was compressed, but zlib extension is not installed.'
            );
        }
        if ($compressed) {
            $content = gzuncompress($stream->read($info['length']));
        } else {
            $content = $stream->read($info['length']);
        }

        $stream->close();
        return $content;
    }

    /**
     * Write compressed file.
     *
     * @param string $key
     * @param string $content
     * @return bool
     */
    public function write($key, $content)
    {
        $compress = $this->_isCompressionAvailable();
        if ($compress) {
            $rawContent = gzcompress($content, $this->_compressRatio);
        } else {
            $rawContent = $content;
        }

        $fileHeaders = pack("ll", (int)$compress, strlen($rawContent));
        return parent::write($key, $fileHeaders . $rawContent);
    }

    /**
     * Create Zlib stream
     *
     * @param string $path
     * @return \Magento\Filesystem\Stream\Zlib
     */
    public function createStream($path)
    {
        return new \Magento\Filesystem\Stream\Zlib($path);
    }

    /**
     * Check that zlib extension loaded.
     *
     * @return bool
     */
    protected function _isCompressionAvailable()
    {
        if (is_null($this->_hasCompression)) {
            $this->_hasCompression = extension_loaded("zlib");
        }
        return $this->_hasCompression;
    }
}
