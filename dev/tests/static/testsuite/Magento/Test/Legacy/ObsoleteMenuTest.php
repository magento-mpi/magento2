<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Legacy tests to find obsolete menu declaration
 */
namespace Magento\Test\Legacy;

class ObsoleteMenuTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $menuFile
     * @dataProvider menuFilesDataProvider
     */
    public function testMenuDeclaration($menuFile)
    {
        $menuXml = simplexml_load_file($menuFile);
        $xpath = '/config/menu/*[boolean(./children) or boolean(./title) or boolean(./action)]';
        $this->assertEmpty(
            $menuXml->xpath($xpath),
            'Obsolete menu structure detected in file ' . $menuFile . '.'
        );
    }

    /**
     * @return array
     */
    public function menuFilesDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getConfigFiles();
    }
}
