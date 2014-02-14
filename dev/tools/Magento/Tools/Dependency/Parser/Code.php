<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Parser;

use Magento\Tools\Dependency\ParserInterface;

/**
 * Code parser
 */
class Code implements ParserInterface
{
    /**
     * Allowed namespaces prefixes
     *
     * @var array
     */
    protected $namespacesPrefixes;

    /**
     * Framework construct
     *
     * @param array $namespacesPrefixes
     */
    public function __construct(array $namespacesPrefixes)
    {
        $this->namespacesPrefixes = $namespacesPrefixes;
    }

    /**
     * Template method. Main algorithm
     *
     * {@inheritdoc}
     */
    public function parse(array $files)
    {
        $pattern = '#\b((?<module>(' . implode('_|', $this->namespacesPrefixes)
            . '[\\\\])[a-zA-Z0-9]+)[a-zA-Z0-9_\\\\]*)\b#';

        $modules = array();
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $module = $this->extractModuleName($file);

            // also collect modules without dependencies
            if (!isset($modules[$module])) {
                $modules[$module] = [
                    'name' => $module,
                    'dependencies' => [],
                ];
            }

            if (preg_match_all($pattern, $content, $matches)) {
                $dependencies = array_count_values($matches['module']);
                foreach ($dependencies as $dependency => $count) {
                    if ($module == $dependency) {
                        continue;
                    }
                    if (isset($modules[$module]['dependencies'][$dependency])) {
                        $modules[$module]['dependencies'][$dependency]['count'] += $count;
                    } else {
                        $modules[$module]['dependencies'][$dependency] = [
                            'lib' => $dependency,
                            'count' => $count,
                        ];
                    }
                }
            }
        }
        return $modules;
    }

    /**
     * Extract module name form file path
     *
     * @param string $file
     * @return string
     */
    protected function extractModuleName($file)
    {
        $pattern = '#code/(?<namespace>' . $this->namespacesPrefixes[0] . ')[/_\\\\]?(?<module>[^/]+)/#';
        if (preg_match($pattern, $file, $matches)) {
            return $matches['namespace'] . '\\' . $matches['module'];
        }
        return '';
    }
}
