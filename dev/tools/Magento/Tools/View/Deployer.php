<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\View;

use Magento\TestFramework\Utility\Files;
use Magento\Framework\App\ObjectManagerFactory;

/**
 * A service for deploying Magento static view files for production mode
 */
class Deployer
{
    /** @var Files */
    private $filesUtil;

    /** @var ObjectManagerFactory */
    private $omFactory;

    /** @var Deployer\Log */
    private $logger;

    /** @var \Magento\Framework\View\Asset\Repository */
    private $assetRepo;

    /** @var \Magento\Framework\App\View\Asset\Publisher */
    private $assetPublisher;

    /** @var bool */
    private $isDryRun;

    /** @var int */
    private $count;

    /** @var int */
    private $errorCount;

    /**
     * @param Files $filesUtil
     * @param Deployer\Log $logger
     * @param bool $isDryRun
     */
    public function __construct(Files $filesUtil, Deployer\Log $logger, $isDryRun = false)
    {
        $this->filesUtil = $filesUtil;
        $this->logger = $logger;
        $this->isDryRun = $isDryRun;
    }

    /**
     * Populate all static view files for specified root path and list of languages
     *
     * @param string $rootPath
     * @param ObjectManagerFactory $omFactory
     * @param array $locales
     */
    public function deploy($rootPath, ObjectManagerFactory $omFactory, array $locales)
    {
        $this->omFactory = $omFactory;
        if ($this->isDryRun) {
            $this->logger->log('Dry run. Nothing will be recorded to the target directory.');
        }
        $langList = implode(', ', $locales);
        $this->logger->log("Requested languages: {$langList}");
        $libFiles = $this->filesUtil->getStaticLibraryFiles();
        list($areas, $appFiles) = $this->collectAppFiles($locales);
        foreach ($areas as $area => $themes) {
            $this->emulateApplicationArea($rootPath, $area);
            foreach ($locales as $locale) {
                foreach ($themes as $themePath) {
                    $this->logger->log("=== {$area} -> {$themePath} -> {$locale} ===");
                    $this->count = 0;
                    $this->errorCount = 0;
                    foreach ($appFiles as $info) {
                        list($fileArea, $fileThemePath, , $module, $filePath) = $info;
                        $this->deployAppFile($area, $fileArea, $themePath, $fileThemePath, $locale, $module, $filePath);
                    }
                    foreach ($libFiles as $filePath) {
                        $this->deployFile($filePath, $area, $themePath, $locale, null);
                    }
                    $this->logger->log("\nSuccessful: {$this->count} files; errors: {$this->errorCount}\n---\n");
                }
            }
        }
    }

    /**
     * Accumulate all static view files in the application and record all found areas, themes and languages
     *
     * Returns an array of areas and files with meta information
     *
     * @param array $requestedLocales
     * @return array
     */
    private function collectAppFiles($requestedLocales)
    {
        $areas = [];
        $locales = [];
        $files = $this->filesUtil->getStaticPreProcessingFiles();
        foreach ($files as $info) {
            list($area, $themePath, $locale) = $info;
            if ($themePath) {
                $areas[$area][$themePath] = $themePath;
            }
            if ($locale) {
                $locales[$locale] = $locale;
            }
        }
        foreach ($requestedLocales as $locale) {
            unset($locales[$locale]);
        }
        if (!empty($locales)) {
            $langList = implode(', ', $locales);
            $this->logger->log(
                "WARNING: there were files for the following languages detected in the file system: {$langList}."
                . ' These languages were not requested, so the files will not be populated.'
            );
        }

        return [$areas, $files];
    }

    /**
     * Emulate application area and various services that are necessary for populating files
     *
     * @param $rootPath
     * @param $areaCode
     */
    private function emulateApplicationArea($rootPath, $areaCode)
    {
        $objectManager = $this->omFactory->create(
            $rootPath,
            [\Magento\Framework\App\State::PARAM_MODE => \Magento\Framework\App\State::MODE_DEFAULT]
        );
        /** @var \Magento\Framework\App\State $appState */
        $appState = $objectManager->get('Magento\Framework\App\State');
        $appState->setAreaCode($areaCode);
        /** @var \Magento\Framework\App\ObjectManager\ConfigLoader $configLoader */
        $configLoader = $objectManager->get('Magento\Framework\App\ObjectManager\ConfigLoader');
        $objectManager->configure($configLoader->load($areaCode));
        $this->assetRepo = $objectManager->get('Magento\Framework\View\Asset\Repository');
        $this->assetPublisher = $objectManager->get('Magento\Framework\App\View\Asset\Publisher');
    }

    /**
     * Deploy a static view file that belongs to the application
     *
     * @param string $area
     * @param string $fileArea
     * @param string $themePath
     * @param string $fileThemePath
     * @param string $locale
     * @param string $module
     * @param string $filePath
     */
    private function deployAppFile($area, $fileArea, $themePath, $fileThemePath, $locale, $module, $filePath)
    {
        if ($fileArea && $fileArea != $area) {
            return;
        }
        if ($fileThemePath && $fileThemePath != $themePath) {
            return;
        }
        $this->deployFile($filePath, $area, $themePath, $locale, $module);
    }

    /**
     * Deploy a static view file
     *
     * @param string $filePath
     * @param string $area
     * @param string $themePath
     * @param string $locale
     * @param string $module
     */
    private function deployFile($filePath, $area, $themePath, $locale, $module)
    {
        $requestedPath = $filePath;
        if (substr($filePath, -5) == '.less') {
            $requestedPath = preg_replace('/.less$/', '.css', $filePath);
        }
        $logModule = $module ? "<{$module}>" : (null === $module ? '<lib>' : '<theme>');
        try {
            $asset = $this->assetRepo->createAsset(
                $requestedPath,
                ['area' => $area, 'theme' => $themePath, 'locale' => $locale, 'module' => $module]
            );
            $this->logger->logDebug("{$logModule} {$filePath} -> {$asset->getPath()}");
            if ($this->isDryRun) {
                $asset->getContent();
            } else {
                $this->assetPublisher->publish($asset);
            }
            $this->count++;
        } catch (\Exception $e) {
            $this->logger->logError("{$logModule} {$filePath}");
            $this->logger->logDebug((string)$e);
            $this->errorCount++;
        }
    }
}
