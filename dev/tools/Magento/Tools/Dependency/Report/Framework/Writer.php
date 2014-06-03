<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Dependency\Report\Framework;

use Magento\Tools\Dependency\Report\Writer\Csv\AbstractWriter;

/**
 * Csv file writer for framework dependencies report
 */
class Writer extends AbstractWriter
{
    /**
     * Template method. Prepare data step
     *
     * @param \Magento\Tools\Dependency\Report\Framework\Data\Config $config
     * @return array
     */
    protected function prepareData($config)
    {
        $data[] = array('Dependencies of framework:', 'Total number');
        $data[] = array('', $config->getDependenciesCount());
        $data[] = array();


        if ($config->getDependenciesCount()) {
            $data[] = array('Dependencies for each module:', '');
            foreach ($config->getModules() as $module) {
                $data[] = array($module->getName(), $module->getDependenciesCount());
                foreach ($module->getDependencies() as $dependency) {
                    $data[] = array(' -- ' . $dependency->getLib(), $dependency->getCount());
                }
                $data[] = array();
            }
        }
        array_pop($data);

        return $data;
    }
}
