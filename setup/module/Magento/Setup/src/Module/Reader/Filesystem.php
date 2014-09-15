<?php
/**
 * Module declaration reader. Reads module.xml declaration files from module /etc directories.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Module\Reader;

use Magento\Config\Reader\Filesystem as ConfigFilesystem;
use Magento\Setup\Module\FileResolver;
use Magento\Setup\Module\Converter\Dom;
use Magento\Setup\Module\SchemaLocator;
use Magento\Setup\Module\Dependency\ManagerInterface;

class Filesystem extends ConfigFilesystem
{
    /**
     * @var \Magento\Setup\Module\Dependency\ManagerInterface
     */
    protected $dependencyManager;

    /**
     * @var array
     */
    protected $idAttributes = array(
        '/config/module' => 'name',
        '/config/module/depends/extension' => 'name',
        '/config/module/depends/choice/extension' => 'name',
        '/config/module/sequence/module' => 'name'
    );

    /**
     * @param FileResolver $fileResolver
     * @param Dom $converter
     * @param SchemaLocator $schemaLocator
     * @param ManagerInterface $dependencyManager
     * @param string $fileName
     * @param string $domDocumentClass
     * @param array $idAttributes
     */
    public function __construct(
        FileResolver $fileResolver,
        Dom $converter,
        SchemaLocator $schemaLocator,
        ManagerInterface $dependencyManager,
        $fileName = 'module.xml',
        $domDocumentClass = '\Magento\Framework\Config\Dom',
        $idAttributes = array()
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $fileName,
            $domDocumentClass,
            $idAttributes
        );
        $this->dependencyManager = $dependencyManager;
    }

    /**
     * Load configuration
     *
     * @return array
     */
    public function read()
    {
        $activeModules = $this->filterActiveModules(parent::read());
        return $this->sortModules($activeModules);
    }

    /**
     * Retrieve declarations of active modules
     *
     * @param array $modules
     * @return array
     */
    protected function filterActiveModules(array $modules)
    {
        $activeModules = array();
        foreach ($modules as $moduleName => $moduleConfig) {
            if ($moduleConfig['active']) {
                $activeModules[$moduleName] = $moduleConfig;
            }
        }
        return $activeModules;
    }

    /**
     * Sort module declarations based on module dependencies
     *
     * @param array $modules
     * @return array
     */
    protected function sortModules(array $modules)
    {
        /**
         * The following map is needed only for sorting
         * (in order not to add extra information about dependencies to module config)
         */
        $moduleDependencyMap = array();
        foreach (array_keys($modules) as $moduleName) {
            $moduleDependencyMap[] = array(
                'moduleName' => $moduleName,
                'dependencies' => $this->dependencyManager->getExtendedModuleDependencies($moduleName, $modules)
            );
        }

        // Use "bubble sorting" because usort does not check each pair of elements and in this case it is important
        $modulesCount = count($moduleDependencyMap);
        for ($i = 0; $i < $modulesCount - 1; $i++) {
            for ($j = $i; $j < $modulesCount; $j++) {
                if (in_array($moduleDependencyMap[$j]['moduleName'], $moduleDependencyMap[$i]['dependencies'])) {
                    $temp = $moduleDependencyMap[$i];
                    $moduleDependencyMap[$i] = $moduleDependencyMap[$j];
                    $moduleDependencyMap[$j] = $temp;
                }
            }
        }

        $sortedModules = array();
        foreach ($moduleDependencyMap as $moduleDependencyPair) {
            $sortedModules[$moduleDependencyPair['moduleName']] = $modules[$moduleDependencyPair['moduleName']];
        }

        return $sortedModules;
    }
}
