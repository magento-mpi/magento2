<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\View\Generator;

/**
 * Transformation of files, which must be copied to new location and its contents processed
 */
class ThemeDeployment
{
    /**
     * Helper to process CSS content and fix urls
     *
     * @var \Magento\Framework\View\Url\CssResolver
     */
    private $_cssUrlResolver;

    /**
     * @var \Magento\Framework\App\View\Deployment\Version\StorageInterface
     */
    private $_versionStorage;

    /**
     * @var \Magento\Framework\App\View\Deployment\Version\GeneratorInterface
     */
    private $_versionGenerator;

    /**
     * Destination dir, where files will be copied to
     *
     * @var string
     */
    private $_destinationHomeDir;

    /**
     * List of extensions for files, which should be deployed.
     * For efficiency it is a map of ext => ext, so lookup by hash is possible.
     *
     * @var array
     */
    private $_permitted = array();

    /**
     * List of extensions for files, which must not be deployed
     * For efficiency it is a map of ext => ext, so lookup by hash is possible.
     *
     * @var array
     */
    private $_forbidden = array();

    /**
     * Whether to actually do anything inside the filesystem
     *
     * @var bool
     */
    private $_isDryRun;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\View\Asset\PreProcessorInterface
     */
    private $preProcessor;

    /**
     * @var \Magento\Framework\View\Publisher\FileFactory
     */
    private $fileFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $tmpDirectory;

    /**
     * @var \Magento\Framework\View\Asset\ModuleNotation\Resolver
     */
    private $_notationResolver;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Url\CssResolver $cssUrlResolver
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Framework\View\Asset\PreProcessorInterface $preProcessor
     * @param \Magento\Framework\View\Publisher\FileFactory $fileFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Core\Model\Theme\DataFactory $themeFactory
     * @param \Magento\Framework\App\View\Deployment\Version\StorageInterface $versionStorage
     * @param \Magento\Framework\App\View\Deployment\Version\GeneratorInterface $versionGenerator
     * @param \Magento\Framework\View\Asset\ModuleNotation\Resolver $notationResolver
     * @param string $destinationHomeDir
     * @param string $configPermitted
     * @param string|null $configForbidden
     * @param bool $isDryRun
     * @throws \Magento\Exception
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Url\CssResolver $cssUrlResolver,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Framework\View\Asset\PreProcessorInterface $preProcessor,
        \Magento\Framework\View\Publisher\FileFactory $fileFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Core\Model\Theme\DataFactory $themeFactory,
        \Magento\Framework\App\View\Deployment\Version\StorageInterface $versionStorage,
        \Magento\Framework\App\View\Deployment\Version\GeneratorInterface $versionGenerator,
        \Magento\Framework\View\Asset\ModuleNotation\Resolver $notationResolver,
        $destinationHomeDir,
        $configPermitted,
        $configForbidden = null,
        $isDryRun = false
    ) {
        $this->themeFactory = $themeFactory;
        $this->appState = $appState;
        $this->preProcessor = $preProcessor;
        $this->tmpDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::VAR_DIR);
        $this->fileFactory = $fileFactory;
        $this->_cssUrlResolver = $cssUrlResolver;
        $this->_versionStorage = $versionStorage;
        $this->_versionGenerator = $versionGenerator;
        $this->_destinationHomeDir = $destinationHomeDir;
        $this->_isDryRun = $isDryRun;
        $this->_notationResolver = $notationResolver;
        $this->_permitted = $this->_loadConfig($configPermitted);
        if ($configForbidden) {
            $this->_forbidden = $this->_loadConfig($configForbidden);
        }
        $conflicts = array_intersect($this->_permitted, $this->_forbidden);
        if ($conflicts) {
            $message = 'Conflicts: the following extensions are added both to permitted and forbidden lists: %s';
            throw new \Magento\Exception(sprintf($message, implode(', ', $conflicts)));
        }
    }

    /**
     * Load config with file extensions
     *
     * @param string $path
     * @return array
     * @throws \Magento\Exception
     */
    protected function _loadConfig($path)
    {
        if (!file_exists($path)) {
            throw new \Magento\Exception("Config file does not exist: {$path}");
        }

        $contents = include $path;
        $contents = array_unique($contents);
        $contents = array_map('strtolower', $contents);
        $contents = $contents ? array_combine($contents, $contents) : array();
        return $contents;
    }

    /**
     * Copy all the files according to $copyRules
     *
     * @param array $copyRules
     * @return void
     */
    public function run($copyRules)
    {
        foreach ($copyRules as $copyRule) {
            $destContext = $copyRule['destinationContext'];
            $context = array(
                'source' => $copyRule['source'],
                'destinationContext' => $destContext,
            );

            $destDir =  $destContext['area'] . '/' . $destContext['themePath'] . '/' . $destContext['module'];
            // $destContext['locale'] is not used as it is not implemented
            $destDir = rtrim($destDir, '\\/');

            $this->_copyDirStructure($copyRule['source'], $this->_destinationHomeDir . '/' . $destDir, $context);
        }

        if (!$this->_isDryRun) {
            $this->_versionStorage->save($this->_versionGenerator->generate());
        }
    }

    /**
     * Copy dir structure and files from $sourceDir to $destinationDir
     *
     * @param string $sourceDir
     * @param string $destinationDir
     * @param array $context
     * @return void
     * @throws \Magento\Exception
     */
    protected function _copyDirStructure($sourceDir, $destinationDir, $context)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($files as $fileSource) {
            $fileSource = (string)$fileSource;
            $extension = strtolower(pathinfo($fileSource, PATHINFO_EXTENSION));
            if ($extension == 'less') {
                $fileSource = preg_replace('/\.less$/', '.css', $fileSource);
            }
            $localPath = substr($fileSource, strlen($sourceDir) + 1);
            $themeModel = $this->themeFactory->create(
                array(
                    'data' => array(
                        'theme_path' => $context['destinationContext']['themePath'],
                        'area' => $context['destinationContext']['area']
                    )
                )
            );
            $fileObject = $this->fileFactory->create(
                $localPath,
                array_merge($context['destinationContext'], array('themeModel' => $themeModel)),
                $fileSource
            );
            /** @var \Magento\Framework\View\Publisher\FileAbstract $fileObject */
            $fileObject = $this->appState->emulateAreaCode(
                $context['destinationContext']['area'],
                array($this->preProcessor, 'process'),
                array($fileObject, $this->tmpDirectory)
            );

            if ($fileObject->getSourcePath()) {
                $fileSource = $fileObject->getSourcePath();
            }

            if (isset($this->_forbidden[$extension])) {
                continue;
            }

            if (!isset($this->_permitted[$extension])) {
                $message = sprintf(
                    'The file extension "%s" must be added either to the permitted or forbidden list. File: %s',
                    $extension,
                    $fileSource
                );
                throw new \Magento\Exception($message);
            }

            if (file_exists($fileSource)) {
                $fileDestination = $destinationDir . '/' . $localPath;
                $this->_deployFile($fileSource, $fileDestination, $context);
            }
        }
    }

    /**
     * Deploy file to the destination path, also processing modular paths inside css-files.
     *
     * @param string $fileSource
     * @param string $fileDestination
     * @param array $context
     * @return void
     * @throws \Magento\Exception
     */
    protected function _deployFile($fileSource, $fileDestination, $context)
    {
        // Create directory
        $destFileDir = dirname($fileDestination);
        if (!is_dir($destFileDir) && !$this->_isDryRun) {
            mkdir($destFileDir, 0777, true);
        }

        // Copy file
        $extension = pathinfo($fileSource, PATHINFO_EXTENSION);
        if (strtolower($extension) == 'css') {
            // For CSS files we need to process content and fix urls
            // Callback to resolve relative urls to the file names
            $filePath = ltrim(str_replace('\\', '/', str_replace($context['source'], '', $fileSource)), '/');
            $assetContext = new \Magento\Framework\View\Asset\File\FallbackContext(
                '',
                $context['destinationContext']['area'],
                $context['destinationContext']['themePath'],
                ''
            );
            $thisAsset = new Asset($assetContext, $filePath, '', 'css');
            $callback = function ($path) use ($thisAsset) {
                return $this->_notationResolver->convertModuleNotationToPath($thisAsset, $path);
            };
            // Replace relative urls and write the modified content (if not dry run)
            $content = file_get_contents($fileSource);
            $content = $this->_cssUrlResolver->replaceRelativeUrls($content, $callback);

            if (!$this->_isDryRun) {
                file_put_contents($fileDestination, $content);
            }
        } else {
            if (!$this->_isDryRun) {
                copy($fileSource, $fileDestination);
            }
        }
    }
}
