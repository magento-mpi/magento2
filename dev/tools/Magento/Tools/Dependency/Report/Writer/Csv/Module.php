<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report\Writer\Csv;

/**
 * Csv file writer for Modules dependencies report
 */
class Module extends AbstractWriter
{
    /**
     * {@inheritdoc}
     */
    protected function prepareData($config)
    {
        $data[] = ['', 'All', 'Hard', 'Soft'];
        $data[] = [
            'Total number of dependencies',
            $config->getDependenciesCount(),
            $config->getHardDependenciesCount(),
            $config->getSoftDependenciesCount(),
        ];
        $data[] = ['', '', ''];

        $data[] = ['Dependencies for each module:', 'All', 'Hard', 'Soft'];
        foreach ($config->getModules() as $module) {
            if ($module->hasDependencies()) {
                $data[] = [
                    $module->getName(),
                    $module->getDependenciesCount(),
                    $module->getHardDependenciesCount(),
                    $module->getSoftDependenciesCount(),
                ];
                foreach ($module->getDependencies() as $dependency) {
                    $data[] = [
                        ' -- ' . $dependency->getModule(),
                        '',
                        (int)$dependency->isHard(),
                        (int)!$dependency->isHard(),
                    ];
                }
                $data[] = ['', '', ''];
            }
        }
        return $data;
    }
}
