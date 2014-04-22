<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

class ViewFilesTest extends \Magento\TestFramework\TestCase\AbstractIntegrity
{
    public function testViewFilesFromModulesView()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
        /**
         * @param string $application
         * @param string $file
         */
            function ($application, $file) {
                \Magento\TestFramework\Helper\Bootstrap::getInstance()
                    ->loadArea($application);
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                    ->get('Magento\Framework\View\DesignInterface')
                    ->setDefaultDesignTheme();
                /** @var \Magento\View\Asset\Repository $assetRepo */
                $assetRepo = \Magento\TestFramework\Helper\Bootstrap::getObjectmanager()
                    ->get('Magento\View\Asset\Repository');
                $result = $assetRepo->createAsset($file)->getSourceFile();

                $fileInfo = pathinfo($result);
                if ($fileInfo['extension'] === 'css') {
                    if (!file_exists($result)) {
                        $file = str_replace('.css', '.less', $file);
                        $result = $assetRepo->createAsset($file)->getSourceFile();
                    };
                }

                $this->assertFileExists($result);
            },
            $this->viewFilesFromModulesViewDataProvider()
        );
    }

    /**
     * Collect usages of templates in base templates
     *
     * @return array
     */
    public function viewFilesFromModulesViewDataProvider()
    {
        $files = array();
        /** @var $configModelReader \Magento\Module\Dir\Reader */
        $configModelReader = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Module\Dir\Reader'
        );
        foreach ($this->_getEnabledModules() as $moduleName) {
            $moduleViewDir = $configModelReader->getModuleDir('view', $moduleName);
            if (!is_dir($moduleViewDir)) {
                continue;
            }
            $this->_findViewFilesInViewFolder($moduleViewDir, $files);
        }
        $result = array();
        foreach ($files as $area => $references) {
            foreach ($references as $file) {
                $result[] = array($area, $file);
            }
        }
        return $result;
    }

    /**
     * Find view file references per area in declared modules.
     *
     * @param string $moduleViewDir
     * @param array $files
     * @return null
     */
    protected function _findViewFilesInViewFolder($moduleViewDir, &$files)
    {
        foreach (new \DirectoryIterator($moduleViewDir) as $viewAppDir) {
            $area = $viewAppDir->getFilename();
            if (0 === strpos($area, '.') || !$viewAppDir->isDir()) {
                continue;
            }
            foreach (new \RecursiveIteratorIterator(
                         new \RecursiveDirectoryIterator($viewAppDir->getRealPath())
                     ) as $fileInfo) {
                $references = $this->_findReferencesToViewFile($fileInfo);
                if (!isset($files[$area])) {
                    $files[$area] = $references;
                } else {
                    $files[$area] = array_merge($files[$area], $references);
                }
                $files[$area] = array_unique($files[$area]);
            }
        }
    }

    /**
     * Scan specified file for getViewFileUrl() pattern
     *
     * @param \SplFileInfo $fileInfo
     * @return array
     */
    protected function _findReferencesToViewFile(\SplFileInfo $fileInfo)
    {
        if (!$fileInfo->isFile() || !preg_match('/\.phtml$/', $fileInfo->getFilename())) {
            return array();
        }

        $result = array();
        $content = file_get_contents($fileInfo->getRealPath());
        if (preg_match_all('/\$this->getViewFileUrl\(\'([^\']+?)\'\)/', $content, $matches)) {
            foreach ($matches[1] as $value) {
                if ($this->_isFileForDisabledModule($value)) {
                    continue;
                }
                $result[] = $value;
            }
        }
        return $result;
    }

    public function testViewFilesFromModulesCode()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
        /**
         * @param string $application
         * @param string $file
         */
            function ($application, $file) {
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')
                    ->setAreaCode($application);
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                    ->get('Magento\Framework\View\DesignInterface')
                    ->setDefaultDesignTheme();
                /** @var \Magento\View\Asset\Repository $assetRepo */
                $assetRepo = \Magento\TestFramework\Helper\Bootstrap::getObjectmanager()
                    ->get('Magento\View\Asset\Repository');
                $this->assertFileExists($assetRepo->createAsset($file)->getSourceFile());
            },
            $this->viewFilesFromModulesCodeDataProvider()
        );
    }

    /**
     * @return array
     */
    public function viewFilesFromModulesCodeDataProvider()
    {
        $allFiles = array();
        foreach (glob(__DIR__ . '/_files/view_files*.php') as $file) {
            $allFiles = array_merge($allFiles, include $file);
        }
        return $this->_removeDisabledModulesFiles($allFiles);
    }

    /**
     * Scans array of file information and removes files, that belong to disabled modules.
     * Thus we won't test them.
     *
     * @param array $allFiles
     * @return array
     */
    protected function _removeDisabledModulesFiles($allFiles)
    {
        $result = array();
        foreach ($allFiles as $fileInfo) {
            $fileName = $fileInfo[1];
            if ($this->_isFileForDisabledModule($fileName)) {
                continue;
            }
            $result[] = $fileInfo;
        }
        return $result;
    }
}
