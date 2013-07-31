<?php
/**
 * Rule for searching php file dependency
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_DependencyTest_PhpRule implements Integrity_DependencyTest_RuleInterface
{
    /**
     * Dynamic regexp for matching class names in modules (based on namespaces list)
     *
     * @var string
     */
    protected $_pattern = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_pattern = '~[\'"\s](?<class>(?<module>(' . implode('_|', Utility_Files::init()->getNamespaces())
            . '_)[a-zA-Z0-9]+)[a-zA-Z0-9_]+)~';
    }

    /**
     * Gets alien dependencies information for current module by analyzing file's contents
     *
     * @param string $currentModule
     * @param string $fileType
     * @param string $file
     * @param string $contents
     * @return array
     */
    public function getDependencyInfo($currentModule, $fileType, $file, $contents)
    {
        if ($fileType != 'php') {
            return array();
        }

        $dependenciesInfo = array();
        if (preg_match_all($this->_pattern, $contents, $matches)) {
            foreach ($matches['module'] as $i => $referenceModule) {
                if ($currentModule == $referenceModule) {
                    continue;
                }
                $dependenciesInfo[] = array('module' => $referenceModule, 'source' => trim($matches['class'][$i]));
            }
        }
        return $dependenciesInfo;
    }
}
