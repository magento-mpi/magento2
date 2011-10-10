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
 * @group module:Mage_Xmlconnect
 */
class Mage_XmlConnect_Helper_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $application
     * @param string $file
     * @dataProvider getSkinImagesUrlDataProvider
     */
    public function testGetSkinImagesUrl($application, $file)
    {
        $helper = new Mage_XmlConnect_Helper_Image;
        Mage::getDesign()->setArea($application)
            ->setPackageName('default')
            ->setTheme('default')
            ->setSkin('default');

        $this->assertStringMatchesFormat(
            "http://%s/media/skin/{$application}/%s/%s/%s/%s/Mage_XmlConnect/images/{$file}", $helper->getSkinImagesUrl($file)
        );
        $this->assertFileExists(Mage::getDesign()->getSkinFile("Mage_XmlConnect::/images/{$file}"));
    }

    /**
     * @return array
     */
    public function getSkinImagesUrlDataProvider()
    {
        return array(
            array('adminhtml', 'dropdown-arrow.gif'),
            array('adminhtml', 'design_default/accordion_open.png'),
            array('adminhtml', 'mobile_preview/1.gif'),
            array('frontend', 'tab_cart.png'),
        );
    }
}
