<?php
/**
 * Coverage of deprecated table names usage
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Legacy_TableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider magentoPhpFilesDataProvider
     */
    public function testLegacyTable($filePath)
    {
        $tables = $this->_extractTables($filePath);
        $legacyTables = array();
        foreach ($tables as $table) {
            $tableName = $table['name'];
            if (strpos($tableName, '/') === false) {
                continue;
            }
            $legacyTables[] = $table;
        }

        $message = $this->_composeFoundsMessage($legacyTables);
        $this->assertEmpty($message, $message);
    }

    /**
     * Returns found table names in a file
     *
     * @param  string $filePath
     * @return array
     */
    protected function _extractTables($filePath)
    {
        $regexpMethods = array(
            '_getRegexpTableInMethods',
            '_getRegexpTableInArrays',
            '_getRegexpTableInProperties'
        );

        $result = array();
        $content = file_get_contents($filePath);
        foreach ($regexpMethods as $method) {
            $regexp = $this->$method($filePath);
            if (!preg_match_all($regexp, $content, $matches, PREG_SET_ORDER)) {
                continue;
            }

            $iterationResult = $this->_matchesToInformation($content, $matches);
            $result = array_merge($result, $iterationResult);
        }
        return $result;
    }

    /**
     * Returns regexp to find table names in method calls in a file
     *
     * @param  string $filePath
     * @return string
     */
    protected function _getRegexpTableInMethods($filePath)
    {
        $methods = array(
            'getTableName',
            '_setMainTable',
            'setMainTable',
            'getTable',
            'setTable',
            'getTableRow',
            'deleteTableRow',
            'updateTableRow',
            'updateTable',
            'tableExists',
            'joinField',
            'joinTable',
            'getFkName',
            'getIdxName',
            array('name' => 'addVirtualGridColumn', 'param_index' => 1)
        );

        if ($this->_isResourceButNotCollection($filePath)) {
            $methods[] = '_init';
        }

        $regexps = array();
        foreach ($methods as $method) {
            $regexps[] = $this->_composeRegexpForMethod($method);
        }
        $result = '#->\s*(' . implode('|', $regexps) . ')#';

        return $result;
    }

    /**
     * @param  string $filePath
     * @return bool
     */
    protected function _isResourceButNotCollection($filePath)
    {
        $filePath = str_replace('\\', '/', $filePath);
        $parts = explode('/', $filePath);
        return (array_search('Resource', $parts) !== false) && (array_search('Collection.php', $parts) === false);
    }

    /**
     * Returns regular expression to find legacy method calls with table in it
     *
     * @param  string|array $method Method name, or array with method name and index of table parameter in signature
     * @return string
     */
    protected function _composeRegexpForMethod($method)
    {
        if (!is_array($method)) {
            $method = array('name' => $method, 'param_index' => 0);
        }

        if ($method['param_index']) {
            $skipParamsRegexp = '\s*[[:alnum:]$_\'"]+\s*,';
            $skipParamsRegexp = str_repeat($skipParamsRegexp, $method['param_index']);
        } else {
            $skipParamsRegexp = '';
        }

        $result = $method['name'] . '\(' . $skipParamsRegexp . '\s*[\'"]([^\'"]+)';
        return $result;
    }

    /**
     * Returns regexp to find table names in array definitions
     *
     * @param  string $filePath
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getRegexpTableInArrays($filePath)
    {
        $keys = array(
            'table',
            'additional_attribute_table',
        );

        $regexps = array();
        foreach ($keys as $key) {
            $regexps[] = '[\'"]' . $key . '[\'"]\s*=>\s*[\'"]([^\'"]+)';
        }
        $result = '#' . implode('|', $regexps) . '#';

        return $result;
    }

    /**
     * Returns regexp to find table names in property assignments
     *
     * @param  string $filePath
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getRegexpTableInProperties($filePath)
    {
        $properties = array(
            '_aggregationTable'
        );

        $regexps = array();
        foreach ($properties as $property) {
            $regexps[] = $property . '\s*=\s*[\'"]([^\'"]+)';
        }
        $result = '#' . implode('|', $regexps) . '#';

        return $result;
    }

    /**
     * Converts regexp matches to information, understandable by human: extracts legacy table name and line,
     * where it was found
     *
     * @param  string $content
     * @param  array $matches
     * @return array
     */
    protected function _matchesToInformation($content, $matches)
    {
        $result = array();
        $fromPos = 0;
        foreach ($matches as $match) {
            $pos = strpos($content, $match[0], $fromPos);
            $lineNum = substr_count($content, "\n", 0, $pos) + 1;
            $result[] = array('name' => $match[count($match) - 1], 'line' => $lineNum);
            $fromPos = $pos + 1;
        }
        return $result;
    }

    /**
     * Composes information message based on list of legacy tables, found in a file
     *
     * @param  array $legacyTables
     * @return null|string
     */
    protected function _composeFoundsMessage($legacyTables)
    {
        if (!$legacyTables) {
            return null;
        }

        $descriptions = array();
        foreach ($legacyTables as $legacyTable) {
            $descriptions[] = "{$legacyTable['name']} (line {$legacyTable['line']})";
        }

        $result = 'Legacy table names with slash must be fixed to direct table names. Found: '
            . implode(', ', $descriptions) . '.';
        return $result;
    }

    /**
     * @return array
     */
    public static function magentoPhpFilesDataProvider()
    {
        $recursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
            PATH_TO_SOURCE_CODE, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS
        ));
        $regexIterator = new RegexIterator($recursiveIterator,
            '#(app/(bootstrap|Mage)\.php | app/code/.+\.php | pub/[a-z]+\.php)$#x'
        );
        $result = array();
        foreach ($regexIterator as $fileInfo) {
            $filePath = (string)$fileInfo;
            $result[] = array($filePath);
        }
        return $result;
    }
}
