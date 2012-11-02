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
 * @group integrity
 */
class Integrity_Mage_XmlConnect_ViewFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that view files are available at both backend and frontend
     *
     * @param string $file
     * @dataProvider sharedViewFilesDataProvider
     */
    public function testSharedViewFiles($file)
    {
        $params = array(
            'area'    => 'adminhtml',
            'package' => 'default',
            'theme'   => 'basic',
        );
        $this->assertFileExists(Mage::getDesign()->getViewFile($file, $params));
        $params['area'] = 'frontend';
        $this->assertFileExists(Mage::getDesign()->getViewFile($file, $params));
    }

    /**
     * @return array
     */
    public function sharedViewFilesDataProvider()
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
