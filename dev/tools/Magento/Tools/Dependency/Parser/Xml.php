<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Parser;

use Magento\Tools\Dependency\Dependency;
use Magento\Tools\Dependency\Module;
use Magento\Tools\Dependency\ParserInterface;

/**
 * Xml parser
 */
class Xml implements ParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse(array $files)
    {
        $modules = array();
        foreach ($files as $file) {
            $config = $this->getModuleConfig($file);
            $modules[] = new Module(
                $this->extractModuleName($config),
                $this->extractDependencies($config)
            );
        }
        return $modules;
    }

    /**
     * @param \SimpleXMLElement $config
     * @return string
     */
    protected function extractModuleName($config)
    {
        return (string)$config->attributes()->name;
    }

    /**
     * @param \SimpleXMLElement $config
     * @return array
     */
    protected function extractDependencies($config)
    {
        $dependencies = array();
        /** @var \SimpleXMLElement $dependency */
        if ($config->depends) {
            foreach ($config->depends->module as $dependency) {
                $dependencies[] = new Dependency(
                    (string)$dependency->attributes()->name,
                    (string)$dependency->attributes()->type
                );
            }
        }
        return $dependencies;
    }

    /**
     * @param string $file
     * @return \SimpleXMLElement
     */
    protected function getModuleConfig($file)
    {
        return \simplexml_load_file($file)->xpath('/config/module')[0];
    }
}
