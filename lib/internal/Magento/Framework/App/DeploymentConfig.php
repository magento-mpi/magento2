<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App;

/**
 * Application deployment configuration
 */
class DeploymentConfig
{
    /**
     * Configuration reader
     *
     * @var DeploymentConfig\Reader
     */
    private $reader;

    /**
     * Configuration data
     *
     * @var array
     */
    private $data;

    /**
     * Injected configuration data
     *
     * @var array
     */
    private $overrideData;

    /**
     * Constructor
     *
     * Data can be optionally injected in the constructor. This object's public interface is intentionally immutable
     *
     * @param DeploymentConfig\Reader $reader
     * @param array $overrideData
     */
    public function __construct(DeploymentConfig\Reader $reader, $overrideData = [])
    {
        $this->reader = $reader;
        $this->overrideData = $overrideData;
    }

    /**
     * Gets data
     *
     * @return array
     */
    public function get()
    {
        $this->load();
        return $this->data;
    }

    /**
     * Checks if data available
     *
     * @return bool
     */
    public function isAvailable()
    {
        $this->load();
        return !empty($this->data);
    }

    /**
     * Gets a value specified key from config data
     *
     * The key is conventionally called "segment". There can be arbitrary data within each segment.
     * This class is agnostic of contents of segments.
     *
     * @param string $key
     * @return null|mixed
     */
    public function getSegment($key)
    {
        $this->load();
        if (!isset($this->data[$key])) {
            return null;
        }
        return $this->data[$key];
    }

    /**
     * Loads the configuration data
     *
     * @return void
     */
    private function load()
    {
        if (null === $this->data) {
            $this->data = $this->reader->load();
            if ($this->overrideData) {
                $this->data = array_replace($this->data, $this->overrideData);
            }
        }
    }
}
