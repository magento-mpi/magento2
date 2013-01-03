<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests to find various obsolete code usage
 * (deprecated and removed Magento 1 legacy methods, properties, classes, etc.)
 */
class Legacy_ObsoleteCodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Message text that is used to render suggestions
     */
    const SUGGESTION_MESSAGE = 'Use "%s" instead.';

    /**@#+
     * Lists of obsolete entities from fixtures
     *
     * @var array
     */
    protected static $_classes    = array();
    protected static $_constants  = array();
    protected static $_methods    = array();
    protected static $_attributes = array();
    /**#@-*/

    /**
     * Read fixtures into memory as arrays
     */
    public static function setUpBeforeClass()
    {
        $errors = array();
        self::_populateList(self::$_classes, $errors, 'obsolete_classes*.php', false);
        self::_populateList(self::$_constants, $errors, 'obsolete_constants*.php');
        self::_populateList(self::$_methods, $errors, 'obsolete_methods*.php');
        self::_populateList(self::$_attributes, $errors, 'obsolete_properties*.php');
        if ($errors) {
            echo 'Duplicate patterns identified in list declarations:' . PHP_EOL . PHP_EOL;
            foreach ($errors as $file => $list) {
                echo $file . PHP_EOL;
                foreach ($list as $key) {
                    echo "    {$key}" . PHP_EOL;
                }
                echo PHP_EOL;
            }
            echo new Exception('Terminating test to avoid scanning entire system with corrupted fixtures.') . PHP_EOL;
            exit(1);
        }
    }

    /**
     * Read the specified file pattern and merge it with the list
     *
     * Duplicate entries will be recorded into errors array.
     *
     * @param array $list
     * @param array $errors
     * @param string $filePattern
     * @param bool $hasScope
     */
    protected static function _populateList(array &$list, array &$errors, $filePattern, $hasScope = true)
    {

        foreach (glob(__DIR__ . '/_files/' . $filePattern, GLOB_BRACE) as $file) {
            foreach (self::_readList($file) as $row) {
                $item = $row[0];
                if ($hasScope) {
                    $scope = isset($row[1]) ? $row[1] : '';
                    $replacement = isset($row[2]) ? $row[2] : '';
                    $dir = isset($row[3]) ? $row[3] : '';
                } else {
                    $scope = '';
                    $replacement = isset($row[1]) ? $row[1] : '';
                    $dir = isset($row[2]) ? $row[2] : '';
                }
                $key = "{$item}|{$scope}";
                if (isset($list[$key])) {
                    $errors[$file][] = $key;
                } else {
                    $list[$key] = array($item, $scope, $replacement, $dir);
                }
            }
        }
    }

    /**
     * Isolate including a file into a method to reduce scope
     *
     * @param $file
     * @return array
     */
    protected static function _readList($file)
    {
        return include($file);
    }

    /**
     * @param string $file
     * @dataProvider phpFileDataProvider
     */
    public function testPhpFile($file)
    {
        $content = file_get_contents($file);
        $this->_testObsoleteClasses($content, $file);
        $this->_testObsoleteMethods($content, $file);
        $this->_testObsoleteMethodArguments($content);
        $this->_testObsoleteProperties($content, $file);
        $this->_testObsoleteActions($content, $file);
        $this->_testObsoleteConstants($content, $file);
        $this->_testObsoletePropertySkipCalculate($content);
    }

    /**
     * @return array
     */
    public function phpFileDataProvider()
    {
        return Utility_Files::init()->getPhpFiles();
    }

    /**
     * @param string $file
     * @dataProvider xmlFileDataProvider
     */
    public function testXmlFile($file)
    {
        $content = file_get_contents($file);
        $this->_testObsoleteClasses($content, $file);
    }

    /**
     * @return array
     */
    public function xmlFileDataProvider()
    {
        return Utility_Files::init()->getXmlFiles();
    }

    /**
     * @param string $file
     * @dataProvider jsFileDataProvider
     */
    public function testJsFile($file)
    {
        $content = file_get_contents($file);
        $this->_testObsoletePropertySkipCalculate($content);
    }

    /**
     * @return array
     */
    public function jsFileDataProvider()
    {
        return Utility_Files::init()->getJsFiles();
    }

    /**
     * @param string $content
     * @param string $file
     */
    protected function _testObsoleteClasses($content, $file)
    {
        foreach (self::$_classes as $row) {
            list($entity, , $suggestion, $dir) = $row;
            if (!$this->_isScopeSkipped($file, $content, '', $dir)) {
                $this->_assertNotRegExp('/[^a-z\d_]' . preg_quote($entity, '/') . '[^a-z\d_]/iS', $content,
                    sprintf("Class '%s' is obsolete. Replacement suggestion: %s", $entity, $suggestion)
                );
            }
        }
    }

    /**
     * Determine if file/content should be skipped based on specified scope/directory
     *
     * @param string $file
     * @param string $content
     * @param string $scope
     * @param string $dir
     * @return bool
     */
    protected function _isScopeSkipped($file, $content, $scope, $dir)
    {
        $regexp = '/(class|extends)\s+' . preg_quote($scope, '/') . '(\s|;)/S';
        /* Note: strpos is used just to prevent excessive preg_match calls */
        if ($scope && (!strpos($content, $scope) || !preg_match($regexp, $content))) {
            return true;
        }
        if ($dir && 0 !== strpos(str_replace('\\', '/', $file), str_replace('\\', '/', $dir))) {
            return true;
        }
        return false;
    }

    /**
     * @param string $content
     * @param string $file
     */
    protected function _testObsoleteMethods($content, $file)
    {
        foreach (self::$_methods as $row) {
            list($method, $scope , $suggestion, $dir) = $row;
            if (!$this->_isScopeSkipped($file, $content, $scope, $dir)) {
                $this->_assertNotRegExp('/[^a-z\d_]' . preg_quote($method, '/') . '\s*\(/iS', $content,
                    sprintf("Method '%s' is obsolete. Replacement suggestion: ", $method, $suggestion)
                );
            }
        }
    }

    /**
     * @param string $content
     */
    protected function _testObsoleteMethodArguments($content)
    {
        $this->_assertNotRegExp('/[^a-z\d_]getTypeInstance\s*\(\s*[^\)]+/iS', $content,
            'Backwards-incompatible change: method getTypeInstance() is not supposed to be invoked with any arguments.'
        );
        $this->_assertNotRegExp('/\->getUsedProductIds\(([^\)]+,\s*[^\)]+)?\)/', $content,
            'Backwards-incompatible change: method getUsedProductIds($product)'
                . ' must be invoked with one and only one argument - product model object'
        );

        $this->_assertNotRegExp('#->_setActiveMenu\([\'"]([\w\d/_]+)[\'"]\)#Ui', $content,
            'Backwards-incompatible change: method _setActiveMenu()'
                . ' must be invoked with menu item identifier than xpath for menu item'
        );

        $this->assertEquals(0,
            preg_match('#Mage::getSingleton\([\'"]Mage_Backend_Model_Auth_Session[\'"]\)'
                . '([\s]+)?->isAllowed\(#Ui', $content),
            'Backwards-incompatible change: method isAllowed()'
                . ' must be invoked from Mage::getSingleton(\'Mage_Code_Model_Authorization\')->isAllowed($resource)'
        );

        $this->_assertNotRegExp(
            '#Mage::getSingleton\([\'"]Mage_Core_Model_Authorization[\'"]\)'
                . '([\s]+)?->isAllowed\([\'"]([\w\d/_]+)[\'"]\)#Ui',
            $content,
            'Backwards-incompatible change: method isAllowed()'
                . ' must be invoked with acl item identifier than xpath for acl item');
    }

    /**
     * @param string $content
     * @param string $file
     */
    protected function _testObsoleteProperties($content, $file)
    {
        foreach (self::$_attributes as $row) {
            list($attribute, $scope , $suggestion, $dir) = $row;
            if (!$this->_isScopeSkipped($file, $content, $scope, $dir)) {
                $this->_assertNotRegExp('/[^a-z\d_]' . preg_quote($attribute, '/') . '[^a-z\d_]/iS', $content,
                    sprintf("Class attribute '%s' is obsolete. Replacement suggestion: %s", $attribute, $suggestion)
                );
            }
        }
    }

    /**
     * @param string $content
     */
    protected function _testObsoleteActions($content)
    {
        $suggestion = 'Resizing images upon the client request is obsolete, use server-side resizing instead';
        $this->_assertNotRegExp('#[^a-z\d_/]catalog/product/image[^a-z\d_/]#iS', $content,
            "Action 'catalog/product/image' is obsolete. $suggestion"
        );
    }

    /**
     * @param string $content
     * @param string $file
     */
    protected function _testObsoleteConstants($content, $file)
    {
        foreach (self::$_constants as $row) {
            list($constant, $scope , $suggestion, $dir) = $row;
            if (!$this->_isScopeSkipped($file, $content, $scope, $dir)) {
                $this->_assertNotRegExp('/[^a-z\d_]' . preg_quote($constant, '/') . '[^a-z\d_]/iS', $content,
                    sprintf("Constant '%s' is obsolete. Replacement suggestion: ", $constant, $suggestion)
                );
            }
        }
    }

    /**
     * @param string $content
     */
    protected function _testObsoletePropertySkipCalculate($content)
    {
        $this->_assertNotRegExp('/[^a-z\d_]skipCalculate[^a-z\d_]/iS', $content,
            "Configuration property 'skipCalculate' is obsolete."
        );
    }

    /**
     * Custom replacement for assertNotRegexp()
     *
     * In this particular test the original assertNotRegexp() cannot be used
     * because of too large text $content, which obfuscates tests output
     *
     * @param string $regex
     * @param string $content
     * @param string $message
     */
    protected function _assertNotRegexp($regex, $content, $message)
    {
        $this->assertSame(0, preg_match($regex, $content), $message);
    }
}
