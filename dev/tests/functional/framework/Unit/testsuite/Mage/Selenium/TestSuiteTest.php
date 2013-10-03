<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once __DIR__ . '/_files/Simple1Test.php';

class Mage_Selenium_TestSuiteTest extends Unit_PHPUnit_TestCase
{
    /**
     * @var Mage_Selenium_TestSuite
     */
    protected $_suite;

    protected function setUp()
    {
        $this->_suite = new Mage_Selenium_TestSuite();
    }

    public function testFilter()
    {
        $filter = new Mage_Test_SkipFilter_Regexp(array('/Simple1Test/'));
        $this->_suite->setTestFilter($filter);
        $this->assertEquals($filter, $this->_suite->getTestFilter());
        $this->_suite->addTest(new Simple1Test('Test'));
        $this->assertEquals(0, $this->_suite->count());
    }

    public function testAddTestMethod()
    {
        $className = 'Simple1Test';
        $suite = new Mage_Selenium_TestSuite(new \ReflectionClass($className), $className,
            new Mage_Test_SkipFilter_Regexp(array('/Not math/')));
        $this->assertCount(2, $suite);
        $suite = new Mage_Selenium_TestSuite(new \ReflectionClass($className), $className,
            new Mage_Test_SkipFilter_Regexp(array('/One/')));
        $this->assertCount(1, $suite);
    }

    public function testAddTestSuite()
    {
        $this->_suite->addTestSuite('Simple1Test');
        $tests = $this->_suite->tests();
        $this->assertCount(1, $tests);
        $this->assertInstanceOf('Mage_Selenium_TestSuite', $tests[0]);
    }

    public function testAddTestFile()
    {
        $this->_suite->addTestFile(__DIR__ . '/_files/Simple2.php');
        $this->assertCount(2, $this->_suite);
        $tests = $this->_suite->tests();
        $this->assertInstanceOf('Mage_Selenium_TestSuite', $tests[0]);
    }

    public function testAddTestFromDirectory()
    {
        $suite = $this->getMock('Mage_Selenium_TestSuite', array('addTestFiles'));
        $path = implode(DIRECTORY_SEPARATOR, array(__DIR__, '_files', 'Simple1Test.php'));
        $suite->expects($this->once())->method('addTestFiles')->with($this->equalTo(array($path)));
        $suite->addTestFromDirectory(__DIR__ . DIRECTORY_SEPARATOR . '_files');
    }
}
