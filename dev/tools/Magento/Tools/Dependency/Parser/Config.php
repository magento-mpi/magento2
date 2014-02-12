<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Parser;

use Magento\Tools\Dependency\ParserInterface;

/**
 * Config parser
 */
class Config implements ParserInterface
{
    /**
     * Template method. Main algorithm
     *
     * {@inheritdoc}
     */
    public function parse(array $files)
    {
        $modules = [];
        foreach ($files as $file) {
            $config = $this->getModuleConfig($file);
            $modules[] = [
                'name' => $this->extractModuleName($config),
                'dependencies' => $this->extractDependencies($config),
            ];
        }
        return $modules;
    }

    /**
     * Template method. Extract module step
     *
     * @param \SimpleXMLElement $config
     * @return string
     */
    protected function extractModuleName($config)
    {
        return (string)$config->attributes()->name;
    }

    /**
     * Template method. Extract dependencies step
     *
     * @param \SimpleXMLElement $config
     * @return array
     */
    protected function extractDependencies($config)
    {
        $dependencies = [];
        /** @var \SimpleXMLElement $dependency */
        if ($config->depends) {
            foreach ($config->depends->module as $dependency) {
                $dependencies[] = [
                    'module' => (string)$dependency->attributes()->name,
                    'type' => (string)$dependency->attributes()->type
                ];
            }
        }
        return $dependencies;
    }

    /**
     * Template method. Load module config step
     *
     * @param string $file
     * @return \SimpleXMLElement
     */
    protected function getModuleConfig($file)
    {
        return \simplexml_load_file($file)->xpath('/config/module')[0];
    }
}
