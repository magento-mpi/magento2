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
    public function getFile($area, ThemeInterface $themeModel, $locale, $file)
    {
        return $this->resolver->resolve($this->getFallbackType(), $file, $area, $themeModel, $locale, null);
    }

    /**
     * @return string
     */
    protected function getFallbackType()
    {
        return \Magento\View\Design\Fallback\RulePool::TYPE_LOCALE_FILE;
    }
}
