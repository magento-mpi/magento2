<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test;

/**
 * Class ApplicationTest
 *
 * @package Magento\Test
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Shell|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shell;

    /**
     * @var \Magento\ToolkitFramework\Application|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * @var string
     */
    protected $_applicationBaseDir;

    /**
     * Set Up before test
     */
    protected function setUp()
    {
        $this->_applicationBaseDir = __DIR__ . '/../../../../../bootstrap.php';
        $this->_shell = $this->getMock('Magento\Shell', array('execute'), array(), '', false);

        $this->_object = new \Magento\ToolkitFramework\Application($this->_applicationBaseDir, $this->_shell);

        $this->_object->applied = array(); // For fixture testing
    }

    /**
     * Tear down after test
     */
    protected function tearDown()
    {
        unset($this->_shell);
        unset($this->_object);
    }

    /**
     * Apply fixtures test
     *
     * @param array $fixtures
     * @param array $expected
     * @dataProvider applyFixturesDataProvider
     */
    public function testApplyFixtures($fixtures, $expected)
    {
        $this->_object->applyFixtures($fixtures);
        $this->assertEquals($expected, $this->_object->applied);
    }

    /**
     * Apply fixture data provider
     *
     * @return array
     */
    public function applyFixturesDataProvider()
    {
        return array(
            'empty fixtures' => array(
                array(),
                array()
            ),
            'fixtures' => array(
                $this->_getFixtureFiles(array('fixture1', 'fixture2')),
                array('fixture1', 'fixture2')
            ),
        );
    }

    /**
     * Apply fixture test
     *
     * @param array $initialFixtures
     * @param array $subsequentFixtures
     * @param array $subsequentExpected
     * @dataProvider applyFixturesSeveralTimesDataProvider
     */
    public function testApplyFixturesSeveralTimes($initialFixtures, $subsequentFixtures, $subsequentExpected)
    {
        $this->_object->applyFixtures($initialFixtures);
        $this->_object->applied = array();
        $this->_object->applyFixtures($subsequentFixtures);
        $this->assertEquals($subsequentExpected, $this->_object->applied);
    }

    /**
     * Apply fixture data provider
     *
     * @return array
     */
    public function applyFixturesSeveralTimesDataProvider()
    {
        return array(
            'no fixtures applied, when sets are same' => array(
                $this->_getFixtureFiles(array('fixture1', 'fixture2')),
                $this->_getFixtureFiles(array('fixture1', 'fixture2')),
                array()
            ),
            'missing fixture applied for a super set' => array(
                $this->_getFixtureFiles(array('fixture1')),
                $this->_getFixtureFiles(array('fixture1', 'fixture2')),
                array('fixture2')
            ),
            'no fixtures applied, when sets were exist before' => array(
                $this->_getFixtureFiles(array('fixture1', 'fixture2')),
                $this->_getFixtureFiles(array('fixture1')),
                array()
            ),
        );
    }

    /**
     * Adds file paths to fixture in a list
     *
     * @param array $fixture
     *
     * @return array
     */
    protected function _getFixtureFiles($fixtures)
    {
        $result = array();
        foreach ($fixtures as $fixture) {
            $result[] = __DIR__ . "/_files/application_test/{$fixture}.php";
        }
        return $result;
    }

}
