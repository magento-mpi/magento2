<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Simple merge strategy - merge anyway
 */
class Mage_Core_Model_Page_Asset_MergeStrategy_Direct
    implements Mage_Core_Model_Page_Asset_MergeStrategy_MergeStrategyInterface
{
    /**
     * @var Magento_Filesystem
     */
    private $_filesystem;

    /**
     * @var Mage_Core_Model_Dir
     */
    private $_dirs;

    /**
     * @var Mage_Core_Helper_Css
     */
    private $_cssHelper;

    /**
     * @var bool
     */
    private $_isCss;

    /**
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Helper_Css $cssHelper
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Helper_Css $cssHelper
    ) {
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        $this->_cssHelper = $cssHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeFiles(array $publicFiles, $destinationFile)
    {
        // Compose content
        $mergedContent = $this->_composeMergedContent($publicFiles, $destinationFile);

        // Save merged content
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->write($destinationFile, $mergedContent);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsCss($isCss)
    {
        $this->_isCss = (bool)$isCss;
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
        $result = array();
        foreach ($publicFiles as $file) {
            if (!$this->_filesystem->has($file)) {
                throw new Magento_Exception("Unable to locate file '{$file}' for merging.");
            }
            $content = $this->_filesystem->read($file);
            if ($this->_isCss) {
                $content = $this->_cssHelper->replaceCssRelativeUrls($content, $file, $targetFile);
            }
            $result[] = $content;
        }
        $result = ltrim(implode($result));
        if ($this->_isCss) {
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
}
