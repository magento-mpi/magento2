<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Dependency\Report\Dependency;

use Magento\Tools\Dependency\Report\Writer\Csv\AbstractWriter;

/**
 * Csv file writer for modules dependencies report
 */
class Writer extends AbstractWriter
{
    /**
     * Template method. Prepare data step
     *
     * @param \Magento\Tools\Dependency\Report\Dependency\Data\Config $config
     * @return array
     */
    protected function prepareData($config)
    {
        $data[] = array('', 'All', 'Hard', 'Soft');
        $data[] = array(
            'Total number of dependencies',
            $config->getDependenciesCount(),
            $config->getHardDependenciesCount(),
            $config->getSoftDependenciesCount()
        );
        $data[] = array();

        if ($config->getDependenciesCount()) {
            $data[] = array('Dependencies for each module:', 'All', 'Hard', 'Soft');
            foreach ($config->getModules() as $module) {
                if ($module->getDependenciesCount()) {
                    $data[] = array(
                        $module->getName(),
                        $module->getDependenciesCount(),
                        $module->getHardDependenciesCount(),
                        $module->getSoftDependenciesCount()
                    );
                    foreach ($module->getDependencies() as $dependency) {
                        $data[] = array(
                            ' -- ' . $dependency->getModule(),
                            '',
                            (int)$dependency->isHard(),
                            (int)(!$dependency->isHard())
                        );
                    }
                    $data[] = array();
                }
            }
        }
        array_pop($data);

        return $data;
    }
}
