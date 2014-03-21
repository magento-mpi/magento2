<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Dependency\Report\Circular;

use Magento\Tools\Dependency\Report\Writer\Csv\AbstractWriter;

/**
 * Csv file writer for circular dependencies report
 */
class Writer extends AbstractWriter
{
    /**
     * Modules chain separator
     */
    const MODULES_SEPARATOR = '->';

    /**
     * Template method. Prepare data step
     *
     * @param \Magento\Tools\Dependency\Report\Circular\Data\Config $config
     * @return array
     */
    protected function prepareData($config)
    {
        $data[] = array('Circular dependencies:', 'Total number of chains');
        $data[] = array('', $config->getDependenciesCount());
        $data[] = array();

        if ($config->getDependenciesCount()) {
            $data[] = array('Circular dependencies for each module:', '');
            foreach ($config->getModules() as $module) {
                $data[] = array($module->getName(), $module->getChainsCount());
                foreach ($module->getChains() as $chain) {
                    $data[] = array(implode(self::MODULES_SEPARATOR, $chain->getModules()));
                }
                $data[] = array();
            }
        }
        array_pop($data);

        return $data;
    }
}
