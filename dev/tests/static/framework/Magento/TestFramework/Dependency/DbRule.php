<?php
/**
 * Rule for searching DB dependency
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Dependency;

class DbRule implements \Magento\TestFramework\Dependency\RuleInterface
{
    /**
     * Map of tables and modules
     *
     * @var array
     */
    protected $_moduleTableMap;

    /**
     * Constructor
     *
     * @param array $tables
     */
    public function __construct(array $tables)
    {
        $this->_moduleTableMap = $tables;
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
        if (!preg_match('#/app/.*/(sql|data|resource)/.*\.php$#', $file)) {
            return array();
        }

        $dependenciesInfo = array();
        $unKnowTables     = array();
        if (preg_match_all('#>gettable(name)?\([\'"]([^\'"]+)[\'"]\)#i', $contents, $matches)) {
            $tables = array_pop($matches);
            foreach ($tables as $table) {
                if (!isset($this->_moduleTableMap[$table])) {
                    $unKnowTables[$file][$table] = $table;
                    continue;
                }
                if (strtolower($currentModule) !== strtolower($this->_moduleTableMap[$table])) {
                    $dependenciesInfo[] = array(
                        'module' => $this->_moduleTableMap[$table],
                        'type'   => \Magento\TestFramework\Dependency\RuleInterface::TYPE_HARD,
                        'source' => $table,
                    );
                }
            }
        }
        foreach ($unKnowTables as $tables) {
            foreach ($tables as $table) {
                $dependenciesInfo[] = array(
                    'module' => 'Unknown',
                    'source' => $table,
                );
            }
        }
        return $dependenciesInfo;
    }
}
