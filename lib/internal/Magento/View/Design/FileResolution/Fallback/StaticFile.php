<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

use Magento\View\Design\ThemeInterface;

/**
 * Provider of static view files
 */
class StaticFile
{
    /**
     * Fallback resolver type
     */
    const TYPE = 'view';

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * Constructor
     *
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Get a static view file name, using fallback mechanism
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string|bool
     */
    public function getFile($area, ThemeInterface $themeModel, $locale, $file, $module = null)
    {
        return $this->resolver->resolve(self::TYPE, $file, $area, $themeModel, $locale, $module);
    }
}
