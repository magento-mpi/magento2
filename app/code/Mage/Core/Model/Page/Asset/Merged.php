<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Iterator that aggregates one or more assets and provides a single public file with equivalent behavior
 */
class Mage_Core_Model_Page_Asset_Merged implements Iterator
{
    /**
     * Sub path for merged files relative to public view cache directory
     */
    const PUBLIC_MERGE_DIR  = '_merged';

    /**
     * @var Magento_ObjectManager
     */
    private $_objectManager;

    /**
     * @var Mage_Core_Model_Design_PackageInterface
     */
    private $_designPackage;

    /**
     * @var Mage_Core_Model_Logger
     */
    private $_logger;

    /**
     * @var Mage_Core_Helper_Css_Processing
     */
    private $_cssHelper;

    /**
     * @var Magento_Filesystem
     */
    private $_filesystem;

    /**
     * @var Mage_Core_Model_Dir
     */
    private $_dirs;

    /**
     * @var Mage_Core_Model_Page_Asset_MergeableInterface[]
     */
    private $_assets;

    /**
     * @var string
     */
    private $_contentType;

    /**
     * Whether initialization has been performed or not
     *
     * @var bool
     */
    private $_isInitialized = false;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Design_PackageInterface $designPackage
     * @param Mage_Core_Model_Logger $logger
     * @param Mage_Core_Helper_Css_Processing $cssHelper
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Dir $dirs
     * @param array $assets
     * @throws InvalidArgumentException
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Design_PackageInterface $designPackage,
        Mage_Core_Model_Logger $logger,
        Mage_Core_Helper_Css_Processing $cssHelper,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Dir $dirs,
        array $assets
    ) {
        $this->_objectManager = $objectManager;
        $this->_designPackage = $designPackage;
        $this->_logger = $logger;
        $this->_filesystem = $filesystem;
        $this->_cssHelper = $cssHelper;
        $this->_dirs = $dirs;

        if (!$assets) {
            throw new InvalidArgumentException('At least one asset has to be passed for merging.');
        }
        /** @var $asset Mage_Core_Model_Page_Asset_MergeableInterface */
        foreach ($assets as $asset) {
            if (!($asset instanceof Mage_Core_Model_Page_Asset_MergeableInterface)) {
                throw new InvalidArgumentException(
                    'Asset has to implement Mage_Core_Model_Page_Asset_MergeableInterface.'
                );
            }
            if (!$this->_contentType) {
                $this->_contentType = $asset->getContentType();
            } else if ($asset->getContentType() != $this->_contentType) {
                throw new InvalidArgumentException(
                    "Content type '{$asset->getContentType()}' cannot be merged with '{$this->_contentType}'."
                );
            }
        }
        $this->_assets = $assets;
    }

    /**
     * Attempt to merge assets, falling back to original non-merged ones, if merging fails
     */
    protected function _initialize()
    {
        if (!$this->_isInitialized) {
            $this->_isInitialized = true;
            try {
                $this->_assets = array($this->_getMergedAsset($this->_assets));
            } catch (Exception $e) {
                $this->_logger->logException($e);
            }
        }
    }

    /**
     * Retrieve asset instance representing a merged file
     *
     * @param Mage_Core_Model_Page_Asset_MergeableInterface[] $assets
     * @return Mage_Core_Model_Page_Asset_AssetInterface
     */
    protected function _getMergedAsset(array $assets)
    {
        $sourceFiles = $this->_getPublicFilesToMerge($assets);
        return $this->_objectManager->create('Mage_Core_Model_Page_Asset_PublicFile', array(
            'file' => $this->_mergeFiles($sourceFiles),
            'contentType' => $this->_contentType,
        ));
    }

    /**
     * Go through all the files to merge, ensure that they are public (publish if needed), and compose
     * array of public paths to merge
     *
     * @param Mage_Core_Model_Page_Asset_MergeableInterface[] $assets
     * @return array
     */
    protected function _getPublicFilesToMerge(array $assets)
    {
        $result = array();
        foreach ($assets as $asset) {
            $publicFile = $this->_designPackage->getViewFilePublicPath($asset->getSourceFile());
            $result[$publicFile] = $publicFile;
        }
        return $result;
    }

    /**
     * Merge files into one
     *
     * @param array $publicFiles
     * @return string
     * @throws Magento_Exception
     */
    protected function _mergeFiles($publicFiles)
    {
        // Extract files to merge
        $mergedFile = $this->_getMergedFilePath($publicFiles);
        $mergedMTimeFile  = $mergedFile . '.dat';

        // Check whether we have already merged these files
        $filesMTimeData = '';
        foreach ($publicFiles as $file) {
            $filesMTimeData .= $this->_filesystem->getMTime($file);
        }
        if ($this->_filesystem->has($mergedFile) && $this->_filesystem->has($mergedMTimeFile)
            && ($filesMTimeData == $this->_filesystem->read($mergedMTimeFile))
        ) {
            return $mergedFile;
        }

        // Compose content
        $mergedContent = $this->_composeMergedContent($publicFiles, $mergedFile);

        // Save merged content
        if (!$this->_filesystem->isDirectory(dirname($mergedFile))) {
            $this->_filesystem->createDirectory(dirname($mergedFile), 0777);
        }
        $this->_filesystem->write($mergedFile, $mergedContent);
        $this->_filesystem->write($mergedMTimeFile, $filesMTimeData);
        return $mergedFile;
    }

    /**
     * Return file name for the resulting merged file
     *
     * @param array $publicFiles
     * @return string
     */
    protected function _getMergedFilePath(array $publicFiles)
    {
        $jsDir = $this->_dirs->getDir(Mage_Core_Model_Dir::PUB_LIB);
        $publicDir = $this->_dirs->getDir(Mage_Core_Model_Dir::STATIC_VIEW);
        $prefixRemovals = array($jsDir, $publicDir);

        $relFileNames = array();
        foreach ($publicFiles as $file) {
            $relFileNames[] = Magento_Filesystem::fixSeparator(str_replace($prefixRemovals, '', $file));
        }

        $mergedDir = $this->_dirs->getDir(Mage_Core_Model_Dir::PUB_VIEW_CACHE) . '/'
            . self::PUBLIC_MERGE_DIR;
        return $mergedDir . '/' . md5(implode('|', $relFileNames)) . '.' . $this->_contentType;
    }

    /**
     * Merge files together and removed merged content
     *
     * @param array $publicFiles
     * @param string $targetFile
     * @return string
     * @throws Magento_Exception
     */
    protected function _composeMergedContent(array $publicFiles, $targetFile)
    {
        $isCss = $this->_contentType == Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS;
        $result = array();
        foreach ($publicFiles as $file) {
            if (!$this->_filesystem->has($file)) {
                throw new Magento_Exception("Unable to locate file '{$file}' for merging.");
            }
            $content = $this->_filesystem->read($file);
            if ($isCss) {
                $callback = function ($relativeUrl) use ($file) {
                    return dirname($file) . '/' . $relativeUrl;
                };
                $content = $this->_cssHelper->replaceCssRelativeUrls($content, $targetFile, $callback);
            }
            $result[] = $content;
        }
        $result = ltrim(implode($result));
        if ($isCss) {
            $result = $this->_popCssImportsUp($result);
        }

        return $result;
    }

    /**
     * Put CSS import directives to the start of CSS content
     *
     * @param string $contents
     * @return string
     */
    protected function _popCssImportsUp($contents)
    {
        $parts = preg_split('/(@import\s.+?;\s*)/', $contents, -1, PREG_SPLIT_DELIM_CAPTURE);
        $imports = array();
        $css = array();
        foreach ($parts as $part) {
            if (0 === strpos($part, '@import', 0)) {
                $imports[] = trim($part);
            } else {
                $css[] = $part;
            }
        }

        $result = implode($css);
        if ($imports) {
            $result = implode("\n", $imports) . "\n" . "/* Import directives above popped up. */\n" . $result;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @return Mage_Core_Model_Page_Asset_AssetInterface
     */
    public function current()
    {
        $this->_initialize();
        return current($this->_assets);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        $this->_initialize();
        return key($this->_assets);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->_initialize();
        next($this->_assets);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->_initialize();
        reset($this->_assets);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        $this->_initialize();
        return (bool)current($this->_assets);
    }
}
