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
     * Declared namespaces
     *
     * @var array
     */
    protected $declaredNamespaces;

    /**
     * Template method. Main algorithm
     *
     * {@inheritdoc}
     */
    public function parse(array $options)
    {
        $this->checkOptions($options);

        $this->declaredNamespaces = $options['declared_namespaces'];

        $pattern = '#\b((?<module>(' . implode(
            '[\\\\]|',
            $this->declaredNamespaces
        ) . '[\\\\])[a-zA-Z0-9]+)[a-zA-Z0-9_\\\\]*)\b#';

        $modules = array();
        foreach ($options['files_for_parse'] as $file) {
            $content = file_get_contents($file);
            $module = $this->extractModuleName($file);

            // also collect modules without dependencies
            if (!isset($modules[$module])) {
                $modules[$module] = array('name' => $module, 'dependencies' => array());
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
                        $modules[$module]['dependencies'][$dependency] = array(
                            'lib' => $dependency,
                            'count' => $count
                        );
                    }
                }
            }
        }
        return $modules;
    }

    /**
     * Template method. Check passed options step
     *
     * @param array $options
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function checkOptions($options)
    {
        if (!isset(
            $options['files_for_parse']
        ) || !is_array(
            $options['files_for_parse']
        ) || !$options['files_for_parse']
        ) {
            throw new \InvalidArgumentException('Parse error: Option "files_for_parse" is wrong.');
        }

        if (!isset(
            $options['declared_namespaces']
        ) || !is_array(
            $options['declared_namespaces']
        ) || !$options['declared_namespaces']
        ) {
            throw new \InvalidArgumentException('Parse error: Option "declared_namespaces" is wrong.');
        }
    }

    /**
     * Extract module name form file path
     *
     * @param string $file
     * @return string
     */
    protected function extractModuleName($file)
    {
        $pattern = '#code/(?<namespace>' . $this->declaredNamespaces[0] . ')[/_\\\\]?(?<module>[^/]+)/#';
        if (preg_match($pattern, $file, $matches)) {
            return $matches['namespace'] . '\\' . $matches['module'];
        }
        return '';
    }
}
