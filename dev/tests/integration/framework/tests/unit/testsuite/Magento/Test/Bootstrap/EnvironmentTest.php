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

/**
 * Test class for Magento_TestFramework_Bootstrap_Environment.
 */
class Magento_Test_Bootstrap_EnvironmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_sessionId = '';

    /**
     * @var Magento_TestFramework_Bootstrap_Environment
     */
    protected $_object;

    public static function setUpBeforeClass()
    {
        self::$_sessionId = session_id();
    }

    public static function tearDownAfterClass()
    {
        session_id(self::$_sessionId);
    }

    protected function setUp()
    {
        $this->_object = new Magento_TestFramework_Bootstrap_Environment();
    }

    protected function tearDown()
    {
        $this->_object = null;
    }

    /**
     * Retrieve the current session's variables
     *
     * @return array|null
     */
    protected function _getSessionVars()
    {
        return (isset($_SESSION) ? $_SESSION : null);
    }

    public function testEmulateHttpRequest()
    {
        $serverVars = $_SERVER;
        $this->assertNotEmpty($serverVars);

        $expectedResult = array('HTTP_HOST' => 'localhost', 'SCRIPT_FILENAME' => 'index.php');
        $actualResult = array('HTTP_HOST' => '127.0.0.1');
        $this->_object->emulateHttpRequest($actualResult);
        $this->assertEquals($expectedResult, $actualResult);

        $this->assertSame($serverVars, $_SERVER, 'Super-global $_SERVER must not be affected.');
    }

    public function testEmulateSession()
    {
        $sessionVars = $this->_getSessionVars();
        $this->assertEmpty(session_id());

        $actualResult = array('session_data_to_be_erased' => 'some_value');
        $this->_object->emulateSession($actualResult);
        $this->assertEquals(array(), $actualResult);

        $this->assertSame($sessionVars, $this->_getSessionVars(), 'Super-global $_SESSION must not be affected.');
        $this->assertNotEmpty(session_id(), 'Global session identified has to be emulated.');
    }
}
