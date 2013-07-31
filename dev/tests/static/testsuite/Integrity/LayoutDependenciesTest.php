<?php
/**
 * Scan source code for incorrect or undeclared modules dependencies
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_LayoutDependenciesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Rules to search dependencies
     *
     * @var array
     */
    protected $_rules = array(
        'ruleCheckAttributeModule',
        'ruleCheckElementBlock',
        'ruleCheckElementAction',
    );

    /**
     * Execute all rules
     *
     * @dataProvider getLayoutFiles
     */
    public function testByRules($file, $namespace, $module)
    {
        $contents = file_get_contents($file);

        $dependencies = array();
        foreach ($this->_rules as $rule) {
            $result = $this->$rule($contents, $namespace, $module);
            if (count($result)) {
                $dependencies = array_merge($dependencies, $result);
            }
        }

        if (count($dependencies)) {
            $this->fail("Undeclared dependency found in $file.\nDependencies: " . implode(", ", $dependencies));
        }
    }

    /**
     * The rule to check dependencies for module="..." attribute
     *
     * @param $contents
     * @param $namespace
     * @param $module
     * @return array
     */
    protected function ruleCheckAttributeModule($contents, $namespace, $module)
    {
        $patterns = array(
            '/<.+module\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)[_](?<module>[A-Z][a-zA-Z]+)[\'"].*>/'
        );
        return $this->searchDependencies($contents, $namespace, $module, $patterns);
    }

    /**
     * The rule to check dependencies for <block> element.
     *
     * Search dependencies for type="..." attribute.
     * Search dependencies for template="..." attribute.
     *
     * @param $contents
     * @param $namespace
     * @param $module
     * @return array
     */
    protected function ruleCheckElementBlock($contents, $namespace, $module)
    {
        $patterns = array(
            '/<block.*type\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-zA-Z]+_?){1,}[\'"].*>/',
            '/<block.*template\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.]+[\'"].*>/'
        );
        return $this->searchDependencies($contents, $namespace, $module, $patterns);
    }

    /**
     * The rule to check dependencies for <action> element.
     *
     * Search dependencies for <block> element.
     * Search dependencies for <template> element.
     * Search dependencies for <file> element.
     * Search dependencies for helper="..." attribute.
     *
     * @param $contents
     * @param $namespace
     * @param $module
     * @return array
     */
    protected function ruleCheckElementAction($contents, $namespace, $module)
    {
        $patterns = array(
            '/<block\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-zA-Z]+_?){1,}<\/block\s*>/',
            '/<template\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.]+<\/template\s*>/',
            '/<file\s*>(?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)::[\w\/\.-]+<\/file\s*>/',
            '<.*helper\s*=\s*[\'"](?<namespace>[A-Z][a-z]+)_(?<module>[A-Z][a-zA-Z]+)_(?:[A-Z][a-z]+_?){1,}::[\w]+[\'"].*>'
        );
        return $this->searchDependencies($contents, $namespace, $module, $patterns);
    }

    /**
     * Search dependencies for defined patterns
     *
     * @param $contents
     * @param $namespace
     * @param $module
     * @param array $patterns
     * @return array
     */
    protected function searchDependencies($contents, $namespace, $module, $patterns = array())
    {
        $result = array();
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $item) {
                    if ($namespace != $item['namespace'] || $module != $item['module']) {
                        $name = $item['namespace'] . '_' . $item['module'];
                        $result[$name] = $name;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Return layout files
     *
     * Return: array(file, namespace, module).
     *
     * @return array
     */
    public function getLayoutFiles()
    {
        $pattern = '/(?<namespace>[A-Z][a-z]+)[_\/](?<module>[A-Z][a-zA-Z]+)/';

        $files = Utility_Files::init()->getLayoutFiles(array(), false);

        $result = array();
        foreach ($files as $file) {
            if (preg_match($pattern, $file, $matches)) {
                $result[$file] = array($file, $matches['namespace'], $matches['module']);
            }
        }
        return $result;
    }
}
