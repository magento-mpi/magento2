<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\Filesystem;

/**
 * A pool of stream wrappers
 */
class DriverPool
{
    /**#@+
     * Available driver types
     */
    const FILE = 'file';
    const HTTP = 'http';
    const HTTPS = 'https';
    const ZLIB = 'compress.zlib';
    /**#@- */

    /**
     * Supported types
     *
     * @var string[]
     */
    protected $types = [
        self::FILE => 'Magento\Framework\Filesystem\Driver\File',
        self::HTTP => 'Magento\Framework\Filesystem\Driver\Http',
        self::HTTPS => 'Magento\Framework\Filesystem\Driver\Https',
        self::ZLIB => 'Magento\Framework\Filesystem\Driver\Zlib',
    ];

    /**
     * The pool
     *
     * @var DriverInterface[]
     */
    private $pool = [];

    /**
     * Obtain extra types in constructor
     *
     * @param string[] $extraTypes
     * @throws \InvalidArgumentException
     */
    public function __construct($extraTypes = [])
    {
        foreach ($extraTypes as $code => $type) {
            if (!is_subclass_of($type, '\Magento\Framework\Filesystem\DriverInterface')) {
                throw new \InvalidArgumentException("The specified type '{$type}' does not implement DriverInterface.");
            }
            $this->types[$code] = $type;
        }
    }

    /**
     * Gets a driver instance by code
     *
     * @param string $code
     * @return DriverInterface
     */
    public function getDriver($code)
    {
        if (!isset($this->types[$code])) {
            $code = self::FILE;
        }
        if (!isset($this->pool[$code])) {
            $class = $this->types[$code];
            $this->pool[$code] = new $class;
        }
        return $this->pool[$code];
    }
}
