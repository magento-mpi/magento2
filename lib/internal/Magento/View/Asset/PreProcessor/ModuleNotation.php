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
     * @var \Magento\View\Asset\PreProcessor\ModuleNotation\Resolver
     */
    private $notationResolver;

    /**
     * @param CssResolver $cssResolver
     * @param ModuleNotation\Resolver $notationResolver
     */
    public function __construct(CssResolver $cssResolver, ModuleNotation\Resolver $notationResolver)
    {
        $this->cssResolver = $cssResolver;
        $this->notationResolver = $notationResolver;
    }

    /**
     * @inheritdoc
     */
    public function process($content, $contentType, Asset\LocalInterface $asset)
    {
        $callback = function ($path) use ($asset) {
            return $this->notationResolver->convertModuleNotationToPath($asset, $path);
        };
        $content = $this->cssResolver->replaceRelativeUrls($content, $callback);
        return array($content, $contentType);
    }
}
