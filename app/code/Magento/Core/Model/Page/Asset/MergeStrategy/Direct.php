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
namespace Magento\Core\Model\Page\Asset\MergeStrategy;

class Direct implements \Magento\Core\Model\Page\Asset\MergeStrategyInterface
{
    /**
     * @var \Magento\Filesystem\Directory\Write
     */
    private $_directory;

    /**
     * @var \Magento\App\Dir
     */
    private $_dirs;

    /**
     * @var \Magento\View\Url\CssResolver
     */
    private $_cssUrlResolver;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\App\Dir $dirs
     * @param \Magento\View\Url\CssResolver $cssUrlResolver
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\App\Dir $dirs,
        \Magento\View\Url\CssResolver $cssUrlResolver
    ) {
        $this->_directory = $filesystem->getDirectoryWrite(\Magento\Filesystem\DirectoryList::PUB);
        $this->_dirs = $dirs;
        $this->_cssUrlResolver = $cssUrlResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeFiles(array $publicFiles, $destinationFile, $contentType)
    {
        $mergedContent = $this->_composeMergedContent($publicFiles, $destinationFile, $contentType);

        $this->_directory->writeFile($this->_directory->getRelativePath($destinationFile), $mergedContent);

    }

    /**
     * Merge files together and modify content if needed
     *
     * @param array $publicFiles
     * @param string $targetFile
     * @param string $contentType
     * @return string
     * @throws \Magento\Exception
     */
    protected function _composeMergedContent(array $publicFiles, $targetFile, $contentType)
    {
        $result = array();
        $isCss = $contentType == \Magento\View\Publisher::CONTENT_TYPE_CSS;

        foreach ($publicFiles as $file) {
            if (!($this->_directory->isExist($this->_directory->getRelativePath($file)))) {
                throw new \Magento\Exception("Unable to locate file '{$file}' for merging.");
            }
            $content = $this->_directory->readFile($this->_directory->getRelativePath($file));
            if ($isCss) {
                $content = $this->_cssUrlResolver->replaceCssRelativeUrls($content, $file, $targetFile);
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
