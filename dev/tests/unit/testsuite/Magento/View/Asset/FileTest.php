<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @param string $expectedErrorMessage
     * @dataProvider extractModuleExceptionDataProvider
     */
    public function testExtractModuleException($file, $expectedErrorMessage)
    {
        $this->setExpectedException('\Magento\Exception', $expectedErrorMessage);
        File::extractModule($file);
    }

    /**
     * @return array
     */
    public function extractModuleExceptionDataProvider()
    {
        return array(
            array('::no_scope.ext', 'Scope separator "::" cannot be used without scope identifier.'),
            array('../file.ext', 'File name \'../file.ext\' is forbidden for security reasons.'),
        );
    }

    public function testExtractModule()
    {
        $this->assertEquals(array('Module_One', 'File'), File::extractModule('Module_One::File'));
        $this->assertEquals(array('', 'File'), File::extractModule('File'));
        $this->assertEquals(
            array('Module_One', 'File::SomethingElse'),
            File::extractModule('Module_One::File::SomethingElse')
        );
    }
}
