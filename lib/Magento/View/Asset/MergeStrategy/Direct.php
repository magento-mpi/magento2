<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Asset\MergeStrategy;

/**
 * Simple merge strategy - merge anyway
 */
class Direct implements \Magento\View\Asset\MergeStrategyInterface
{
    /**#@+
     * Delimiters for merging files of various content type
     */
    const MERGE_DELIMITER_JS = ';';

    const MERGE_DELIMITER_EMPTY = '';

    /**#@-*/

    /**
     * Directory Write
     *
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    private $_directory;

    /**
     * Css Resolver
     *
     * @var \Magento\View\Url\CssResolver
     */
    protected $cssUrlResolver;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\View\Url\CssResolver $cssUrlResolver
     */
    public function __construct(
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\View\Url\CssResolver $cssUrlResolver
    ) {
        $this->_directory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::PUB_DIR);
        $this->_cssUrlResolver = $cssUrlResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeFiles(array $publicFiles, $destinationFile, $contentType)
    {
        $mergedContent = $this->composeMergedContent($publicFiles, $destinationFile, $contentType);
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
    protected function composeMergedContent(array $publicFiles, $targetFile, $contentType)
    {
        $result = array();
        $isCss = $contentType == \Magento\View\Publisher::CONTENT_TYPE_CSS ? true : false;
        $delimiter = $this->_getFilesContentDelimiter($contentType);

        foreach ($publicFiles as $file) {
            if (!$this->_directory->isExist($this->_directory->getRelativePath($file))) {
                throw new \Magento\Exception("Unable to locate file '{$file}' for merging.");
            }
            $content = $this->_directory->readFile($this->_directory->getRelativePath($file));
            if ($isCss) {
                $content = $this->_cssUrlResolver->replaceCssRelativeUrls($content, $file, $targetFile);
            }
            $result[] = $content;
        }
        $result = ltrim(implode($delimiter, $result));
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
     * Return delimiter for separation of merged files content
     *
     * @param string $contentType
     * @return string
     */
    protected function _getFilesContentDelimiter($contentType)
    {
        if ($contentType == \Magento\View\Publisher::CONTENT_TYPE_JS) {
            return self::MERGE_DELIMITER_JS;
        }
        return self::MERGE_DELIMITER_EMPTY;
    }
}
