<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\MergeStrategy;

use Magento\View\Asset;

/**
 * The actual merging service
 */
class Direct implements \Magento\View\Asset\MergeStrategyInterface
{
    /**
     * @var \Magento\App\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\View\Url\CssResolver
     */
    private $cssUrlResolver;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Url\CssResolver $cssUrlResolver
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Url\CssResolver $cssUrlResolver
    ) {
        $this->filesystem = $filesystem;
        $this->cssUrlResolver = $cssUrlResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $assetsToMerge, Asset\LocalInterface $resultAsset)
    {
        /** @var Asset\File $resultAsset */
        $mergedContent = $this->composeMergedContent($assetsToMerge, $resultAsset);
        $dir = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $dir->writeFile($resultAsset->getRelativePath(), $mergedContent);
    }

    /**
     * Merge files together and modify content if needed
     *
     * @param \Magento\View\Asset\MergeableInterface[] $assetsToMerge
     * @param \Magento\View\Asset\LocalInterface $resultAsset
     * @return string
     * @throws \Magento\Exception
     */
    private function composeMergedContent(array $assetsToMerge, Asset\LocalInterface $resultAsset)
    {
        $dir = $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $result = array();
        /** @var Asset\FileId $asset */
        foreach ($assetsToMerge as $asset) {
            $file = $dir->getRelativePath($asset->getSourceFile());
            if (!$dir->isExist($file)) {
                throw new \Magento\Exception("Unable to locate file '{$file}' for merging.");
            }
            $content = $dir->readFile($file);
            $content = $this->preProcessBeforeMerging($asset, $resultAsset, $content);
            $result[] = $content;
        }
        $result = $this->preProcessMergeResult($resultAsset, ltrim(implode($result)));
        return $result;
    }

    /**
     * Process an asset before merging into resulting asset
     *
     * @param Asset\LocalInterface $item
     * @param Asset\LocalInterface $result
     * @param string $content
     * @return string
     */
    private function preProcessBeforeMerging(Asset\LocalInterface $item, Asset\LocalInterface $result, $content)
    {
        if ($result->getContentType() == 'css') {
            $from = $item->getRelativePath();
            $to = $result->getRelativePath();
            return $this->cssUrlResolver->relocateRelativeUrls($content, $from, $to);
        }
        return $content;
    }

    /**
     * Process the resulting asset after merging content is done
     *
     * @param Asset\LocalInterface $result
     * @param string $content
     * @return string
     */
    private function preProcessMergeResult(Asset\LocalInterface $result, $content)
    {
        if ($result->getContentType() == 'css') {
            $content = $this->cssUrlResolver->aggregateImportDirectives($content);
        }
        return $content;
    }
}
