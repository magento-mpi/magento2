<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Stub.php';

class Magento_Test_ClearProperties_DummyTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    public $testPublic;
    private $_testPrivate;

    /**
     * @var boolean
     */
    protected $_testPropertyBoolean;

    /**
     * @var integer
     */
    protected $_testPropertyInteger;

    /**
     * @var float
     */
    protected $_testPropertyFloat;

    /**
     * @var string
     */
    protected $_testPropertyString;

    /**
     * @var array
     */
    protected $_testPropertyArray;

    /**
     * @var mixed
     */
    protected $_testPropertyObject;

    /**
     * @var string
     */
    static public $testPublicStatic;
    static protected $_testProtectedStatic;
    static private $_testPrivateStatic;

    public function testDummy()
    {
        $this->testPublic = 'public';
        $this->_testPrivate = 'private';
        $this->_testPropertyBoolean = true;
        $this->_testPropertyInteger = 10;
        $this->_testPropertyFloat = 1.97;
        $this->_testPropertyString = 'string';
        $this->_testPropertyArray = array('test', 20);
        $this->_testPropertyObject = new Magento_Test_ClearProperties_Stub();
        self::$testPublicStatic = 'static public';
        self::$_testProtectedStatic = 'static protected';
        self::$_testPrivateStatic = 'static private';
    }
}
