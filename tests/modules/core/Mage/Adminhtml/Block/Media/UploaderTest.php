<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_PackageName
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Adminhtml_Block_Media_Uploader Test Case
 */
class Mage_Adminhtml_Block_Media_UploaderTest extends Mage_TestCase
{
    /**
     * Media Uploader instance
     *
     * @var Mage_Adminhtml_Block_Media_Uploader
     */
    protected $_uploader;

    public function setUp()
    {
        $this->_uploader = new Mage_Adminhtml_Block_Media_Uploader();
    }

    /**
     *
     *
     * @dataProvider uploaderUrlDataProvider
     * @see bug #15471
     * @group bugs
     */
    public function testGetUploaderUrl($expectsUrl, $outputUrl)
    {
        $this->assertEquals($expectsUrl, $this->_uploader->getUploaderUrl($outputUrl));
    }

    /**
     * Provides test unit with data
     *
     * @return array test data
     */
    public function uploaderUrlDataProvider()
    {
        //changing base skin url to new
        $fixture = $this->_getFixture();
        $cfg = $fixture->loadFixture('fixtures/set_new_skin_url.xml');
        $fixture->applyConfig($cfg, true);

        $design = Mage::getDesign()->setArea('adminhtml')->setTheme('default');

        $defaultTheme = $design->getDefaultTheme();
        $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'skin/' .
            $design->getArea() . '/' . $design->getPackageName() . '/';

        return array(
            array($baseUrl . $defaultTheme . '/', $this->_uploader),
            array($baseUrl . $defaultTheme . '/', 1.23),
            array($baseUrl . $defaultTheme . '/', array(1)),
            array($baseUrl . $defaultTheme . '/', true),
            array($baseUrl . $defaultTheme . '/', null),
            //an existing uploader SWF in skin "default"
            array($baseUrl . $defaultTheme . '/media/uploader.swf', 'media/uploader.swf'),
            //not existing uploader SWF
            array($baseUrl . $defaultTheme . '/media/bla-bla.swf', 'media/bla-bla.swf')
        );
    }
}