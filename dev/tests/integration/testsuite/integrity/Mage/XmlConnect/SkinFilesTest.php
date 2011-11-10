<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_XmlConnect
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_XmlConnect
 * @group integrity
 */
class Integrity_Mage_XmlConnect_SkinFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that skin files are available at both backend and frontend
     *
     * @param string $file
     * @dataProvider sharedSkinFilesDataProvider
     */
    public function testSharedSkinFiles($file)
    {
        $params = array(
            '_area'    => 'adminhtml',
            '_package' => 'default',
            '_theme'   => 'default',
        );
        $this->assertFileExists(Mage::getDesign()->getSkinFile($file, $params));
        $params['_area'] = 'frontend';
        $this->assertFileExists(Mage::getDesign()->getSkinFile($file, $params));
    }

    /**
     * @return array
     */
    public function sharedSkinFilesDataProvider()
    {
        return array(
            array('Mage_XmlConnect::images/tab_home.png'),
            array('Mage_XmlConnect::images/tab_shop.png'),
            array('Mage_XmlConnect::images/tab_search.png'),
            array('Mage_XmlConnect::images/tab_cart.png'),
            array('Mage_XmlConnect::images/tab_more.png'),
            array('Mage_XmlConnect::images/tab_account.png'),
            array('Mage_XmlConnect::images/tab_page.png'),
        );
    }
}
