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
 * Class for load test suite by Class Name
 */
class Mage_Test_Loader extends PHPUnit_Runner_StandardTestSuiteLoader
{
    /**
     * Load test suit by name from file
     *
     * @param  string  $suiteClassName
     * @param  string  $suiteClassFile
     * @throws PHPUnit_Framework_Exception
     * @return ReflectionClass
     */
    public function load($suiteClassName, $suiteClassFile = '')
    {
        $suiteClassName = str_replace('.php', '', $suiteClassName);

        if (empty($suiteClassFile)) {
            $suiteClassFile = PHPUnit_Util_Filesystem::classNameToFilename(
                $suiteClassName
            );
        }

        if (!class_exists($suiteClassName, FALSE)) {
            PHPUnit_Util_Class::collectStart();
            $filename = PHPUnit_Util_Fileloader::checkAndLoad($suiteClassFile);
            $loadedClasses = PHPUnit_Util_Class::collectEnd();
        }

        if (!class_exists($suiteClassName, FALSE) && !empty($loadedClasses)) {
            $offset = 0 - strlen($suiteClassName);

            foreach ($loadedClasses as $loadedClass) {
                $class = new \ReflectionClass($loadedClass);
                if (substr($loadedClass, $offset) === $suiteClassName
                    && $class->getFileName() == $filename
                ) {
                    $suiteClassName = $loadedClass;
                    break;
                }
            }
        }

        if (!class_exists($suiteClassName, FALSE) && !empty($loadedClasses)) {
            $testCaseClass = 'PHPUnit_Framework_TestCase';

            foreach ($loadedClasses as $loadedClass) {
                $class = new \ReflectionClass($loadedClass);
                $classFile = $class->getFileName();

                if ($class->isSubclassOf($testCaseClass)
                    && !$class->isAbstract()
                ) {
                    $suiteClassName = $loadedClass;
                    $testCaseClass = $loadedClass;

                    if ($classFile == realpath($suiteClassFile)) {
                        break;
                    }
                }

                if ($class->hasMethod('suite')) {
                    $method = $class->getMethod('suite');

                    if (!$method->isAbstract()
                        && $method->isPublic()
                        && $method->isStatic()
                    ) {
                        $suiteClassName = $loadedClass;

                        if ($classFile == realpath($suiteClassFile)) {
                            break;
                        }
                    }
                }
            }
        }

        if (class_exists($suiteClassName, FALSE)) {
            $class = new \ReflectionClass($suiteClassName);
            if ($class->getFileName() == $filename) {
                return $class;
            }
        }

        throw new PHPUnit_Framework_Exception(
            sprintf(
                'Class %s could not be found in %s.',
                $suiteClassName,
                $suiteClassFile
            )
        );
    }
}
