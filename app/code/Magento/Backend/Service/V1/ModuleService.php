<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Service\V1;

/**
 * Module service.
 */
class ModuleService implements ModuleServiceInterface
{
    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;
    /**
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Returns an array of enabled modules
     *
     * @return string[]
     */
    public function getModules()
    {
        $result = [];
        $modules = $this->moduleList->getModules();
        foreach ($modules as $module) {
            $result[] = $module['name'];
        }
        return $result;
    }
}
