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

class Mage_XmlConnect_Helper_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @param string $file
     * @dataProvider getSkinImagesUrlDataProvider
     */
    public function testGetSkinImagesUrl($area, $file)
    {
        $helper = new Mage_XmlConnect_Helper_Image;
        Mage::getDesign()->setArea($area);

        $this->assertStringMatchesFormat(
            "http://%s/media/theme/{$area}/%s/%s/%s/Mage_XmlConnect/images/{$file}",
            $helper->getSkinImagesUrl($file)
        );
        $this->assertFileExists(Mage::getDesign()->getViewFile("Mage_XmlConnect::/images/{$file}"));
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
