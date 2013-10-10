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

/**
 * Test suite class
 */
class Mage_Selenium_TestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * @var Mage_Test_SkipFilter
     */
    protected $_testCaseFilter;

    /**
     * @param mixed $theClass
     * @param string $name
     * @param Mage_Test_SkipFilter|null $testCaseFilter
     */
    public function __construct($theClass = '', $name = '', Mage_Test_SkipFilter $testCaseFilter = null)
    {
        $this->_testCaseFilter = $testCaseFilter;
        parent::__construct($theClass, $name);
    }

    /**
     * Set test skip filter
     *
     * @param Mage_Test_SkipFilter $testFilter
     * @return Mage_Selenium_TestSuite
     */
    public function setTestFilter(Mage_Test_SkipFilter $testFilter)
    {
        $this->_testCaseFilter = $testFilter;
        return $this;
    }

    /**
     * Get test skip filter
     *
     * @throws RuntimeException
     * @return Mage_Test_SkipFilter|null
     */
    public function getTestFilter()
    {
        if (!$this->_testCaseFilter instanceof Mage_Test_SkipFilter) {
            throw new RuntimeException('Filter is not properly initialized');
        }
        return $this->_testCaseFilter;
    }

    /**
     * Adds a test to the suite.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  array $groups
     */
    public function addTest(PHPUnit_Framework_Test $test, $groups = array())
    {
        $name = $test instanceof PHPUnit_Framework_SelfDescribing ? $test->toString() : get_class($test);
        if (empty($this->_testCaseFilter) || !$this->_testCaseFilter->filter($name)) {
            parent::addTest($test, $groups);
        }
    }

    /**
     * Adds the tests from the given class to the suite.
     *
     * @param  mixed $testClass
     *
     * @throws InvalidArgumentException
     * @throws PHPUnit_Framework_Exception
     * @return void
     */
    public function addTestSuite($testClass)
    {
        if (is_string($testClass) && class_exists($testClass)) {
            $testClass = new \ReflectionClass($testClass);
        }

        if (!is_object($testClass)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(
                1, 'class name or object'
            );
        }

        if ($testClass instanceof PHPUnit_Framework_TestSuite) {
            $this->addTest($testClass);
        } else if ($testClass instanceof ReflectionClass) {
            $suiteMethod = FALSE;

            if (!$testClass->isAbstract()) {
                if ($testClass->hasMethod(PHPUnit_Runner_BaseTestRunner::SUITE_METHODNAME)) {
                    $method = $testClass->getMethod(
                        PHPUnit_Runner_BaseTestRunner::SUITE_METHODNAME
                    );

                    if ($method->isStatic()) {
                        $this->addTest(
                            $method->invoke(NULL, $testClass->getName(), $this->_testCaseFilter)
                        );

                        $suiteMethod = TRUE;
                    }
                }
            }

            if (!$suiteMethod && !$testClass->isAbstract()) {
                $this->addTest(new static($testClass));
            }
        } else {
            throw new InvalidArgumentException;
        }
    }

    /**
     * Add test method
     *
     * @param ReflectionClass  $class
     * @param \ReflectionMethod $method
     */
    protected function addTestMethod(ReflectionClass $class, \ReflectionMethod $method)
    {
        $name = $class->getName() . '::' . $method->getName();
        if (empty($this->_testCaseFilter) || !$this->_testCaseFilter->filter($name)) {
            parent::addTestMethod($class, $method);
        }
    }

    /**
     * Add test file
     *
     * @param string $filename
     * @param array $phptOptions
     *
     * @throws PHPUnit_Framework_Exception
     * @return void
     */
    public function addTestFile($filename, $phptOptions = array())
    {
        if (!is_string($filename)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
        }

        if (file_exists($filename) && substr($filename, -5) == '.phpt') {
            $this->addTest(
                new PHPUnit_Extensions_PhptTestCase($filename, $phptOptions)
            );

            return;
        }

        PHPUnit_Util_Class::collectStart();
        $filename = PHPUnit_Util_Fileloader::checkAndLoad($filename);
        $newClasses = PHPUnit_Util_Class::collectEnd();
        $baseName = str_replace('.php', '', basename($filename));

        foreach ($newClasses as $className) {
            if (substr($className, 0 - strlen($baseName)) == $baseName) {
                $class = new \ReflectionClass($className);

                if ($class->getFileName() == $filename) {
                    $newClasses = array($className);
                    break;
                }
            }
        }

        foreach ($newClasses as $className) {
            $class = new \ReflectionClass($className);

            if (!$class->isAbstract()) {
                if ($class->hasMethod(PHPUnit_Runner_BaseTestRunner::SUITE_METHODNAME)) {
                    $method = $class->getMethod(
                        PHPUnit_Runner_BaseTestRunner::SUITE_METHODNAME
                    );
                    if ($method->isStatic()) {
                        $this->addTest($method->invoke(NULL, $className, $this->_testCaseFilter));
                    }
                } else if ($class->implementsInterface('PHPUnit_Framework_Test')) {
                    $this->addTestSuite($class);
                }
            }
        }

        $this->numTests = -1;
    }

    /**
     * Add all test from directory by mask *Test.php
     * @param string $directory
     * @param string $baseDirectory
     */
    public function addTestFromDirectory($directory, $baseDirectory = '')
    {
        $filesIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );
        $files = array();
        /* @var $fileInfo SplFileInfo */
        foreach ($filesIterator as $fileInfo) {
            if (!$fileInfo->isDir() && preg_match('/Test.php/', $fileInfo->getBasename())) {
                $files[] = str_replace($baseDirectory, '', $fileInfo->getRealPath());
            }
        }
        $this->addTestFiles($files);
    }
}