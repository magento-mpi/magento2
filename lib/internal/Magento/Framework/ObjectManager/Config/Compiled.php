<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\Config;

use Magento\Framework\ObjectManager\ConfigCache;
use Magento\Framework\ObjectManager\Relations;

class Compiled implements \Magento\Framework\ObjectManager\Config
{
    public function __construct($data)
    {
        $this->arguments = $data['arguments'];
        $this->nonShared = $data['nonShared'];
        $this->instanceTypes = $data['instanceTypes'];
        $this->preferences = $data['preferences'];
    }

    /**
     * Set class relations
     *
     * @param Relations $relations
     *
     * @return void
     */
    public function setRelations(Relations $relations)
    {

    }

    /**
     * Set configuration cache instance
     *
     * @param ConfigCache $cache
     *
     * @return void
     */
    public function setCache(ConfigCache $cache)
    {

    }

    /**
     * Retrieve list of arguments per type
     *
     * @param string $type
     * @return array
     */
    public function getArguments($type)
    {
        if (isset($this->arguments[$type])) {
            return $this->arguments[$type];
        } else {
            return ['Magento\Framework\ObjectManager'];
        }
    }

    /**
     * Check whether type is shared
     *
     * @param string $type
     * @return bool
     */
    public function isShared($type)
    {
        return !isset($this->nonShared[$type]);
    }

    /**
     * Retrieve instance type
     *
     * @param string $instanceName
     * @return mixed
     */
    public function getInstanceType($instanceName)
    {
        return isset($this->instanceTypes[$instanceName]) ? $this->instanceTypes[$instanceName] : $instanceName;
    }

    /**
     * Retrieve preference for type
     *
     * @param string $type
     * @return string
     * @throws \LogicException
     */
    public function getPreference($type)
    {
        return isset($this->preferences[$type]) ? $this->preferences[$type] : $type;
    }

    /**
     * Extend configuration
     *
     * @param array $configuration
     * @return void
     */
    public function extend(array $configuration)
    {
        $this->arguments = $configuration['arguments'];
        $this->nonShared = $configuration['nonShared'];
        $this->instanceTypes = $configuration['instanceTypes'];
        $this->preferences = $configuration['preferences'];
    }
} 
