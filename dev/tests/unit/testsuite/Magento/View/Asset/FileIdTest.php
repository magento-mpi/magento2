<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

class FileIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @expectedException \Magento\Exception
     * @dataProvider extractScopeExceptionDataProvider
     */
    public function testExtractScopeException($file)
    {
        FileId::extractModule($file);
    }

    /**
     * @return array
     */
    public function extractScopeExceptionDataProvider()
    {
        return array(
            array('::no_scope.ext'),
            array('../file.ext'),
        );
    }
} 
