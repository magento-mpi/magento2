<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App;

use Magento\Framework\App\DeploymentConfig\DbConfig;
use Magento\Framework\App\DeploymentConfig\ResourceConfig;

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
     * Flattened data
     *
     * @var array
     */
    private $flatData;

    /**
     * Injected configuration data
     *
     * @var array
     */
    private $overrideData;

    /**
     * Availability of deployment config file
     *
     * @var bool
     */
    private $isAvailable;

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
     * Gets data from flattened data
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return array|null
     */
    public function get($key = null, $defaultValue = null)
    {
        $this->load();
        if ($key === null) {
            return $this->flatData;
        }
        return isset($this->flatData[$key]) ? $this->flatData[$key] : $defaultValue;
    }

    /**
     * Checks if data available
     *
     * @return bool
     */
    public function isAvailable()
    {
        $this->load();
        return $this->isAvailable;
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
     * Reload config.php
     *
     * @return void
     */
    public function reload()
    {
        $this->data = null;
        $this->load();
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
            $this->isAvailable = !empty($this->data);
            if ($this->overrideData) {
                $this->data = array_replace($this->data, $this->overrideData);
            }
            // flatten data for config retrieval using get()
            $this->flatData = $this->flattenParams($this->data);
        }
    }

    /**
     * Convert associative array of arbitrary depth to a flat associative array with concatenated key path as keys
     *
     * @param array $params
     * @return array
     */
    private function flattenParams(array $params)
    {

        $result = array();
        $stack = $params;
        while ($stack) {
            list($key, $value) = each($stack);
            unset($stack[$key]);
            if (is_array($value)) {
                if (count($value)) {
                    foreach ($value as $subKey => $node) {
                        $build[$key . '/' . $subKey] = $node;
                    }
                    if (array_key_exists($key, $build)) {
                        unset($build[$key]);
                    }
                } else {
                    $build[$key] = null;
                }
                $stack = $build + $stack;
                continue;
            }
            $result[$key] = $value;
        }
        return $result;
    }
}
