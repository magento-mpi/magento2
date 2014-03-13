<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor;

use Magento\View\Asset;
use Magento\View\FileSystem;
use Magento\View\Url\CssResolver;

/**
 * Support of notation "Module_Name::file/path.ext" in CSS-files
 *
 * This implementation is specific to CSS, despite that the actual algorithm of calculating offsets is generic.
 * The part specific to CSS is the fact that a CSS file may refer to another file and the relative path has to be
 * based off the current location of CSS-file. So context of base path can be known ONLY at the moment
 * of traversing the CSS contents in context of the file location.
 * Other than that, the algorithm of resolving notation "Module_Name::file/path.ext" is generic
 */
class ModuleNotation implements Asset\PreProcessorInterface
{
    /**
     * @var \Magento\View\Url\CssResolver
     */
    private $cssResolver;

    /**
     * @param CssResolver $cssResolver
     */
    public function __construct(CssResolver $cssResolver)
    {
        $this->cssResolver = $cssResolver;
    }

    /**
     * @inheritdoc
     */
    public function process($content, $contentType, Asset\LocalInterface $asset)
    {
        $callback = function ($path) use ($asset) {
            return self::convertModuleNotationToPath($asset, $path);
        };
        $content = $this->cssResolver->replaceRelativeUrls($content, $callback);
        return array($content, $contentType);
    }

    /**
     * Convert module notation to a path relative to the specified asset
     *
     * For example, the asset is Foo_Bar/styles/style.css and it refers to Bar_Baz::images/logo.gif
     * (i.e. url(Bar_Baz::images/logo.gif))
     * The result will be ../../Bar_Baz/images/logo.gif
     *
     * The $asset has to be of "FileId" type, because only it carries necessary relevant "params" information.
     * Its method "createSimilar()" exists because the $params are not exposed by the $asset object (encapsulated).
     * Carrying params may seem an excessive constraint, but the problem is that currently interfaces of view services
     * include an optional $params argument, so anyone from client code may want to pass something in $params.
     * Therefore while $params is supported across the board, they have to be encapsulated into "FileId" asset
     * to prevent further deterioration of design.
     *
     * @param Asset\FileId $thisAsset
     * @param string $relatedFileId
     * @return string
     */
    public static function convertModuleNotationToPath(Asset\FileId $thisAsset, $relatedFileId)
    {
        if (false === strpos($relatedFileId, Asset\FileId::FILE_ID_SEPARATOR)) {
            return $relatedFileId;
        }
        $thisPath = $thisAsset->getRelativePath();
        $relatedPath = $thisAsset->createSimilar($relatedFileId)->getRelativePath();
        $offset = FileSystem::offsetPath($relatedPath, $thisPath);
        return FileSystem::normalizePath($offset . '/' . basename($relatedPath));
    }
}
