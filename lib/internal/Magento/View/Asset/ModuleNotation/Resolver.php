<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\ModuleNotation;

use Magento\View\Asset;
use Magento\View\FileSystem;

class Resolver
{
    /**
     * @var \Magento\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @param Asset\Repository $assetRepo
     */
    public function __construct(\Magento\View\Asset\Repository $assetRepo)
    {
        $this->assetRepo = $assetRepo;
    }

    /**
     * Convert module notation to a path relative to the specified asset
     *
     * For example, the asset is Foo_Bar/styles/style.css and it refers to Bar_Baz::images/logo.gif
     * (i.e. url(Bar_Baz::images/logo.gif))
     * The result will be ../../Bar_Baz/images/logo.gif
     *
     * @param Asset\LocalInterface $thisAsset
     * @param string $relatedFileId
     * @return string
     */
    public function convertModuleNotationToPath(Asset\LocalInterface $thisAsset, $relatedFileId)
    {
        if (false === strpos($relatedFileId, Asset\Repository::FILE_ID_SEPARATOR)) {
            return $relatedFileId;
        }
        $thisPath = $thisAsset->getPath();
        $relatedAsset = $this->assetRepo->createSimilar($relatedFileId, $thisAsset);
        $relatedPath = $relatedAsset->getPath();
        $offset = FileSystem::offsetPath($relatedPath, $thisPath);
        return FileSystem::normalizePath($offset . '/' . basename($relatedPath));
    }
}
