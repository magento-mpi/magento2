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
 * Provider of dynamic view files
 */
class File
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
     * Get existing file name, using fallback mechanism
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $file
     * @param string|null $module
     * @return string|bool
     */
    public function getFile($area, ThemeInterface $themeModel, $file, $module = null)
    {
        return $this->resolver->resolve($this->getFallbackType(), $file, $area, $themeModel, null, $module);
    }

    /**
     * @return string
     */
    protected function getFallbackType()
    {
        return \Magento\View\Design\Fallback\RulePool::TYPE_FILE;
    }
}
