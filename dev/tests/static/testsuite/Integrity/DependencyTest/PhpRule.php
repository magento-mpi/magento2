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
     * Gets alien dependencies information for current module by analyzing file's contents
     *
     * @param string $currentModule
     * @param string $fileType
     * @param string $file
     * @param string $contents
     * @return array
     */
    public function getDependencyInfo($currentModule, $fileType, $file, &$contents)
    {
        if (!in_array($fileType, array('php', 'config'))) {
            return array();
        }

        $pattern = '~\b(?<class>(?<module>(' . implode('_|', Utility_Files::init()->getNamespaces())
            . '_)[a-zA-Z0-9]+)[a-zA-Z0-9_]*)\b~';

        $dependenciesInfo = array();
        if (preg_match_all($pattern, $contents, $matches)) {
            $matches['module'] = array_unique($matches['module']);
            foreach ($matches['module'] as $i => $referenceModule) {
                if ($currentModule == $referenceModule || $referenceModule == 'Mage_Exception') {
                    continue;
                }
                $dependenciesInfo[] = array(
                    'module' => $referenceModule,
                    'type'   => Integrity_DependencyTest::DEPENDENCY_TYPE_HARD,
                    'source' => trim($matches['class'][$i]),
                );
            }
        }
        return $dependenciesInfo;
    }
}
