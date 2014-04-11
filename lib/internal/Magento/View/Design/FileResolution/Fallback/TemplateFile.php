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
 * Provider of template view files
 */
class TemplateFile
{
    /**
     * Fallback resolver type
     */
    const TYPE = 'template';

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
     * Get existing file name, using fallback mechanism
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $file
     * @param string|null $module
     * @return string|bool
     */
    public function getTemplateFile($area, ThemeInterface $themeModel, $file, $module = null)
    {
        return $this->resolver->resolve(self::TYPE, $file, $area, $themeModel, null, $module);
    }
}
