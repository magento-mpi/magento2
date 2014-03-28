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
    public function convertModuleNotationToPath(Asset\FileId $thisAsset, $relatedFileId)
    {
        if (false === strpos($relatedFileId, Asset\FileId::FILE_ID_SEPARATOR)) {
            return $relatedFileId;
        }
        $thisPath = $thisAsset->getRelativePath();
        $relatedPath = $thisAsset->createRelative($relatedFileId)->getRelativePath();
        $offset = FileSystem::offsetPath($relatedPath, $thisPath);
        return FileSystem::normalizePath($offset . '/' . basename($relatedPath));
    }
}
