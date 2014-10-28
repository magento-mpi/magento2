<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Config;

use Zend\Config\Config as ZendConfig;
use Zend\Filter\Inflector;

class Config extends ZendConfig
{
    /**
     * @var Inflector
     */
    private $inflector;

    /**
     * @param Inflector $inflector
     * @param array $array
     */
    public function __construct(Inflector $inflector, array $array)
    {
        $this->inflector = $inflector;
        $this->inflector->setTarget(':name')
            ->setRules([':name' => ['Word\CamelCaseToUnderscore', 'StringToLower']]);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->data[$key] = new static($this->inflector, $value);
            } else {
                $this->data[$key] = $value;
            }
            $this->count++;
        }
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $name = $this->inflector->filter(['name' => $name]);

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return $default;
    }

    /**
     * Retrieve Magento base path
     *
     * @return string
     */
    public function getMagentoBasePath()
    {
        return $this->magento->basePath;
    }

    /**
     * Retrieve path to Magento modules
     *
     * @return string
     */
    public function getMagentoModulePath()
    {
        return $this->magento->filesystem->module;
    }

    /**
     * Retrieve path to Magento config directory
     *
     * @return mixed
     */
    public function getMagentoConfigPath()
    {
        return $this->magento->filesystem->config;
    }
}
