<?php
/**
 * Module declaration reader. Reads module.xml declaration files from module /etc directories.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module\Declaration\Reader;

use Magento\Framework\Module\Declaration\FileResolver;
use Magento\Framework\Module\Declaration\Converter\Dom;
use Magento\Framework\Module\Declaration\SchemaLocator;
use Magento\Framework\Module\DependencyManagerInterface;
use Magento\Framework\Config\ValidationStateInterface;

class Filesystem extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * Allowed modules
     */
    const PARAM_ALLOWED_MODULES = 'allowed_modules';
    /**
     * The list of allowed modules
     *
     * @var array
     */
    protected $_allowedModules;

    /**
     * @var \Magento\Framework\Module\DependencyManagerInterface
     */
    protected $dependencyManager;

    /**
     * @var array
     */
    protected $_idAttributes = array(
        '/config/module' => 'name',
        '/config/module/depends/extension' => 'name',
        '/config/module/depends/choice/extension' => 'name',
        '/config/module/sequence/module' => 'name'
    );

    /**
     * @param FileResolver $fileResolver
     * @param Dom $converter
     * @param SchemaLocator $schemaLocator
     * @param ValidationStateInterface $validationState
     * @param DependencyManagerInterface $dependencyManager
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     * @param array $allowedModules
     */
    public function __construct(
        FileResolver $fileResolver,
        Dom $converter,
        SchemaLocator $schemaLocator,
        ValidationStateInterface $validationState,
        DependencyManagerInterface $dependencyManager,
        $fileName = 'module.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'global',
        array $allowedModules = array()
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
        $this->_allowedModules = $allowedModules;
        $this->dependencyManager = $dependencyManager;
    }

    /**
     * {@inheritdoc}
     */
    public function read($scope = null)
    {
        $activeModules = $this->_filterActiveModules(parent::read($scope));
        foreach ($activeModules as $moduleConfig) {
            $this->dependencyManager->checkModuleDependencies($moduleConfig, $activeModules);
        }
        return $this->_sortModules($activeModules);
    }

    /**
     * Retrieve declarations of active modules
     *
     * @param array $modules
     * @return array
     */
    protected function _filterActiveModules(array $modules)
    {
        $activeModules = array();
        foreach ($modules as $moduleName => $moduleConfig) {
            if ($moduleConfig['active'] && (empty($this->_allowedModules) || in_array(
                $moduleConfig['name'],
                $this->_allowedModules
            ))
            ) {
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
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _sortModules(array $modules)
    {
        /**
         * The following map is needed only for sorting
         * (in order not to add extra information about dependencies to module config)
         */
        $moduleDependencyMap = array();
        foreach ($modules as $moduleName => $value) {
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
