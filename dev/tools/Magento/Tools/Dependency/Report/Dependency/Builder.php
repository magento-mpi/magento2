<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report\Dependency;

use Magento\Tools\Dependency\Report\Builder\AbstractBuilder;

/**
 *  Dependencies report builder
 */
class Builder extends AbstractBuilder
{
    /**
     * Template method. Prepare data for writer step
     *
     * @param array $modulesData
     * @return \Magento\Tools\Dependency\Report\Dependency\Data\Config
     */
    protected function prepareData($modulesData)
    {
        $modules = array();
        foreach ($modulesData as $moduleData) {
            $dependencies = array();
            foreach ($moduleData['dependencies'] as $dependencyData) {
                $dependencies[] = new Data\Dependency($dependencyData['module'], $dependencyData['type']);
            }
            $modules[] = new Data\Module($moduleData['name'], $dependencies);
        }
        return new Data\Config($modules);
    }
}
