<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Transformation of files, which must be copied to new location and its contents processed
 */
namespace Magento\Tools\View\Generator;

class ThemeDeployment
{
    /**
     * Helper to process CSS content and fix urls
     *
     * @var \Magento\View\Url\CssResolver
     */
    private $_cssUrlResolver;

    /**
     * @var \Magento\App\View\Deployment\Version\StorageInterface
     */
    private $_versionStorage;

    /**
     * @var \Magento\App\View\Deployment\Version\GeneratorInterface
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
     * @var \Magento\View\Asset\ModuleNotation\Resolver
     */
    private $_notationResolver;

    /**
     * Constructor
     *
     * @param \Magento\View\Url\CssResolver $cssUrlResolver
     * @param \Magento\App\View\Deployment\Version\StorageInterface $versionStorage
     * @param \Magento\App\View\Deployment\Version\GeneratorInterface $versionGenerator
     * @param \Magento\View\Asset\ModuleNotation\Resolver $notationResolver
     * @param string $destinationHomeDir
     * @param string $configPermitted
     * @param string|null $configForbidden
     * @param bool $isDryRun
     * @throws \Magento\Exception
     */
    public function __construct(
        \Magento\View\Url\CssResolver $cssUrlResolver,
        \Magento\App\View\Deployment\Version\StorageInterface $versionStorage,
        \Magento\App\View\Deployment\Version\GeneratorInterface $versionGenerator,
        \Magento\View\Asset\ModuleNotation\Resolver $notationResolver,
        $destinationHomeDir,
        $configPermitted,
        $configForbidden = null,
        $isDryRun = false
    ) {
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

        $contents = include($path);
        $contents = array_unique($contents);
        $contents = array_map('strtolower', $contents);
        $contents = $contents ? array_combine($contents, $contents) : array();
        return $contents;
    }

    /**
     * Copy all the files according to $copyRules
     *
     * @param array $copyRules
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

            $this->_copyDirStructure(
                $copyRule['source'],
                $this->_destinationHomeDir . '/' . $destDir,
                $context
            );
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
     * @throws \Magento\Exception
     */
    protected function _copyDirStructure($sourceDir, $destinationDir, $context)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($files as $fileSource) {
            $fileSource = (string) $fileSource;
            $extension = strtolower(pathinfo($fileSource, PATHINFO_EXTENSION));

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

            $fileDestination = $destinationDir . substr($fileSource, strlen($sourceDir));
            $this->_deployFile($fileSource, $fileDestination, $context);
        }
    }

    /**
     * Deploy file to the destination path, also processing modular paths inside css-files.
     *
     * @param string $fileSource
     * @param string $fileDestination
     * @param array $context
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
        if (strtolower($extension) == 'css') { // For CSS files we need to process content and fix urls
            // Callback to resolve relative urls to the file names
            $fileId = ltrim(str_replace('\\', '/', str_replace($context['source'], '', $fileSource)), '/');
            $assetContext = new \Magento\View\Asset\File\FallbackContext(
                '',
                $context['destinationContext']['area'],
                $context['destinationContext']['themePath'],
                ''
            );
            $thisAsset = new \Magento\Tools\View\Generator\Asset($assetContext, $fileId, 'css');
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
