<?php
/**
 * Rule for searching DB dependency
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_DependencyTest_DbRule implements Integrity_DependencyTest_RuleInterface
{
    /**
     * Map of tables and modules
     *
     * @var array
     */
    protected $_moduleTableMap;

    /**
     * Regexp for matching table names in scripts
     *
     * @var string
     */
    protected $_pattern;

    /**
     * Regexp for matching file path
     *
     * @var string
     */
    protected $_dbFilePattern;

    /**
     * Constructor
     */
    public function __construct()
    {
        $replaceFilePattern = str_replace('\\', '/', realpath(__DIR__)) . '/_files/*.php';
        $this->_moduleTableMap = array();
        foreach (glob($replaceFilePattern) as $fileName) {
            $tables = @include $fileName;
            $this->_moduleTableMap = array_merge($this->_moduleTableMap, $tables);
        }

        $this->_pattern = '#>gettable(name)?\([\'"]([^\'"]+)[\'"]\)#i';
        $this->_dbFilePattern = '#/app/.*/(sql|data|resource)/.*\.php$#';
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
    public function getDependencyInfo($currentModule, $fileType, $file, &$contents)
    {
        if (!preg_match($this->_dbFilePattern, $file)) {
            return array();
        }

        $dependenciesInfo = array();
        $unKnowTables     = array();
        if (preg_match_all($this->_pattern, $contents, $matches)) {
            $tables = array_pop($matches);
            foreach ($tables as $table) {
                if (!isset($this->_moduleTableMap[$table])) {
                    $unKnowTables[$file][$table] = $table;
                    continue;
                }
                if (strtolower($currentModule) !== strtolower($this->_moduleTableMap[$table])) {
                    $dependenciesInfo[] = array(
                        'module' => $this->_moduleTableMap[$table],
                        'source' => "{$table}::{$file}",
                    );
                }
            }
        }
        foreach ($unKnowTables as $file => $tables) {
            foreach ($tables as $table) {
                $dependenciesInfo[] = array(
                    'module' => 'Unknown',
                    'source' => "{$table}::{$file}",
                );
            }
        }
        return $dependenciesInfo;
    }
}
