<?php
/**
 * Backwards-incompatible changes in file system
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Legacy;

class PhtmlTemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test usage of protected and private methods and variables in template
     *
     * According to naming convention (B5.8, B6.2) all class members
     * in protected or private scope should be prefixed with underscore.
     * Member variables declared "public" should never start with an underscore.
     * Access to protected and private members of Block class is obsolete in phtml templates
     * since introduction of multiple template engines support
     *
     * @param string $file
     * @dataProvider phtmlFilesDataProvider
     */
    public function testObsoleteBlockMethods($file)
    {
        $this->assertNotRegexp('/this->_[^_]+\S*\(/iS',
            file_get_contents($file),
            'Access to protected and private members of Block class is ' .
            'obsolete in phtml templates. Use only public members.');
    }

    /**
     * @return array
     */
    public function phtmlFilesDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getPhtmlFiles();
    }
}
