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
    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\App\Dir
     */
    protected $dirs;

    /**
     * @var \Magento\View\Url\CssResolver
     */
    protected $cssUrlResolver;

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
        $this->filesystem = $filesystem;
        $this->dirs = $dirs;
        $this->cssUrlResolver = $cssUrlResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeFiles(array $publicFiles, $destinationFile, $contentType)
    {
        $mergedContent = $this->composeMergedContent($publicFiles, $destinationFile, $contentType);

        $this->filesystem->setIsAllowCreateDirectories(true);
        $this->filesystem->write($destinationFile, $mergedContent);
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
        $isCss = $contentType == \Magento\View\Publisher::CONTENT_TYPE_CSS;

        foreach ($publicFiles as $file) {
            if (!$this->filesystem->has($file)) {
                throw new \Magento\Exception("Unable to locate file '{$file}' for merging.");
            }
            $content = $this->filesystem->read($file);
            if ($isCss) {
                $content = $this->cssUrlResolver->replaceCssRelativeUrls($content, $file, $targetFile);
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
