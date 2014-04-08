<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

use Magento\App\Filesystem;
use Magento\View\Design\Fallback\Rule\RuleInterface;
use Magento\Filesystem\Directory\Read;

/**
 * Resolver for dynamic view files, which performs full search of files, according to fallback rules
 */
class Resolver
{
    /**
     * Get path of file after using fallback rules
     *
     * @param Read $directory
     * @param RuleInterface $fallbackRule
     * @param string $file
     * @param array $params
     * @return string|bool
     */
    public function resolveFile(Read $directory, RuleInterface $fallbackRule, $file, array $params = array())
    {
        foreach ($fallbackRule->getPatternDirs($params) as $dir) {
            $path = "{$dir}/{$file}";
            if ($directory->isExist($directory->getRelativePath($path))) {
                return $path;
            }
        }
        return false;
    }
}
