<?php
/**
 * The framework implementation of the PHPUnit_Runner_TestSuiteLoader
 */
class TestSuiteLoader extends PHPUnit_Runner_StandardTestSuiteLoader
{
    /**
     * @param  string  $suiteClassName
     * @param  string  $suiteClassFile
     * @param  boolean $syntaxCheck
     * @return ReflectionClass
     * @throws RuntimeException
     */
    public function load($suiteClassName, $suiteClassFile = '', $syntaxCheck = FALSE)
    {
        if ('' === $suiteClassFile && false === strpos($suiteClassName, '/') && false === strpos($suiteClassName, '_')) {
            $suiteClassName = Core::getTestSuiteClassName($suiteClassName);
        }
        return parent::load($suiteClassName, $suiteClassFile, $syntaxCheck);
    }
}
