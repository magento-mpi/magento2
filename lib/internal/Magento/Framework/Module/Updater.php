<?php
/**
 * Application module updater. Used to install/upgrade module data.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module;

class Updater
{
    /**
     * @var ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var ResourceResolverInterface
     */
    protected $_resourceResolver;

    /**
     * @var Updater\SetupFactory
     */
    protected $_setupFactory;

    /**
     * @var DbVersionDetector
     */
    private $_dbVersionDetector;

    /**
     * @param Updater\SetupFactory $setupFactory
     * @param ModuleListInterface $moduleList
     * @param ResourceResolverInterface $resourceResolver
     * @param DbVersionDetector $dbVersionDetector
     */
    public function __construct(
        Updater\SetupFactory $setupFactory,
        ModuleListInterface $moduleList,
        ResourceResolverInterface $resourceResolver,
        DbVersionDetector $dbVersionDetector
    ) {
        $this->_moduleList = $moduleList;
        $this->_resourceResolver = $resourceResolver;
        $this->_setupFactory = $setupFactory;
        $this->_dbVersionDetector = $dbVersionDetector;
    }

    /**
     * Apply database data updates whenever needed
     *
     * @return void
     */
    public function updateData()
    {
        foreach ($this->_moduleList->getNames() as $moduleName) {
            foreach ($this->_resourceResolver->getResourceList($moduleName) as $resourceName) {
                if (!$this->_dbVersionDetector->isDbDataUpToDate($moduleName, $resourceName)) {
                    $this->_setupFactory->create($resourceName, $moduleName)->applyDataUpdates();
                }
            }
        }
    }
}
