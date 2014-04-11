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
 * Provider of localized view files
 */
class LocaleFile
{
    /**
     * Fallback resolver type
     */
    const TYPE = 'locale';

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
     * Get locale file name, using fallback mechanism
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $locale
     * @param string $file
     * @return string|bool
     */
    public function getLocaleFile($area, ThemeInterface $themeModel, $locale, $file)
    {
        return $this->resolver->resolve(self::TYPE, $file, $area, $themeModel, $locale, null);
    }
}
