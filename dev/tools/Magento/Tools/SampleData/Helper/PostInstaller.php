<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Tools\SampleData\Helper;

use Magento\Tools\SampleData\SetupInterface;

class PostInstaller
{
    /**
     * @var array
     */
    protected $setupList;

    /**
     * @var array
     */
    protected $installedModules;

    /**
     * @param SetupInterface $setupResource
     * @param int $sortOrder
     * @return $this
     */
    public function addSetupResource(SetupInterface $setupResource, $sortOrder = 10)
    {
        if (!isset($this->setupList[$sortOrder])) {
            $this->setupList[$sortOrder] = [];
        }
        $this->setupList[$sortOrder][] = $setupResource;
        return $this;
    }

    /**
     * @param string $moduleName
     * @return $this
     */
    public function addModule($moduleName)
    {
        $this->installedModules[] = $moduleName;
        return $this;
    }

    /**
     * @return array
     */
    public function getInstalledModuleList()
    {
        return $this->installedModules;
    }

    /**
     * Launch post install process
     *
     * @return $this
     */
    public function run()
    {
        foreach ($this->setupList as $setupResources) {
            foreach ($setupResources as $resource) {
                $resource->run();
            }
        }
        return $this;
    }
}
