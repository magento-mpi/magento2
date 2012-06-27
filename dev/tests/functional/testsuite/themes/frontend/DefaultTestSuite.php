<?php
class DefaultTestSuite extends Mage_Selenium_TestSuite
{
    public static function suite()
    {
        Mage_Selenium_TestConfiguration::getInstance(array(
            'fallbackOrderFixture' => array('enterprise', 'default')
        ));

        $suite = new self();
        $suite->setTestFilter(new Mage_Test_SkipFilter_Regexp(array(
                '/createEntryPointAuto/'
        )));
        $suite->addTestFromDirectory(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'testsuite/');
        return $suite;
    }
}