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
class Magento_Core_Model_Page_Asset_MergeStrategy_Direct implements Magento_Core_Model_Page_Asset_MergeStrategyInterface
{
    /**
     * @var \Magento\Filesystem
     */
    private $_filesystem;

    /**
     * @var Magento_Core_Model_Dir
     */
    private $_dirs;

    /**
     * @var Magento_Core_Helper_Css
     */
    private $_cssHelper;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Helper_Css $cssHelper
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        Magento_Core_Model_Dir $dirs,
        Magento_Core_Helper_Css $cssHelper
    ) {
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        $this->_cssHelper = $cssHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeFiles(array $publicFiles, $destinationFile, $contentType)
    {
        $mergedContent = $this->_composeMergedContent($publicFiles, $destinationFile, $contentType);

        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->write($destinationFile, $mergedContent);
    }

    /**
     * Merge files together and modify content if needed
     *
     * @param array $publicFiles
     * @param string $targetFile
     * @param string $contentType
     * @return string
     * @throws \Magento\MagentoException
     */
    protected function _composeMergedContent(array $publicFiles, $targetFile, $contentType)
    {
        $result = array();
        $isCss = $contentType == Magento_Core_Model_View_Publisher::CONTENT_TYPE_CSS;

        foreach ($publicFiles as $file) {
            if (!$this->_filesystem->has($file)) {
                throw new \Magento\MagentoException("Unable to locate file '{$file}' for merging.");
            }
            $content = $this->_filesystem->read($file);
            if ($isCss) {
                $content = $this->_cssHelper->replaceCssRelativeUrls($content, $file, $targetFile);
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
}
