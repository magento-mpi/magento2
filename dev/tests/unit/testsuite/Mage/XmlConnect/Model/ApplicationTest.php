<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_XmlConnect
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_XmlConnect_Model_ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareSubmitParams()
    {
        /** @var $model Mage_XmlConnect_Model_Application */
        $model = $this->getMock(
            'Mage_XmlConnect_Model_Application',
            array('getHelperData', 'getHelperImage'),
            array(),
            '',
            false
        );

        $imageIds = array('icon', 'ipad_loader_portrait_image', 'ipad_loader_landscape_image', 'ipad_logo', 'big_logo');
        $helperData = $this->getMock('Mage_XmlConnect_Helper_Data', array('getDeviceHelper'), array(), '', false);
        $helperData->expects($this->once())->method('getDeviceHelper')->with()
            ->will($this->returnValue(new Varien_Object(array('submit_images' => $imageIds ))));
        $model->expects($this->once())->method('getHelperData')->with()->will($this->returnValue($helperData));

        $methodName = 'getDefaultSizeUploadDir';
        $helperImage = $this->getMock('Mage_XmlConnect_Helper_Image', array($methodName), array(), '', false);
        $helperImage->expects($this->exactly(2))->method($methodName)->with()
            ->will($this->returnValue('/pub/media/xmlconnect/custom/320x480'));
        $model->expects($this->exactly(2))->method('getHelperImage')->with()->will($this->returnValue($helperImage));

        $params = $model->prepareSubmitParams(array());
        $this->assertEquals(array(), $params);
        $this->assertEquals(array(), $model->getSubmitParams());

        $model->setData(array(
            'name'     => 'Application Name',
            'code'     => 'defipa1',
            'type'     => 'ipad',
            'store_id' => 1,
            'url'      => 'http://localhost/index.php/xmlconnect/configuration/index/app_code/defipa1/',
            'conf'     => array(
                'submit' => array(
                    'icon'                        => 'icon.png',
                    'loader_image'                => 'loader_image.png',
                    'loader_image_i4'             => 'loader_image_i4.png',
                    'logo'                        => 'logo.png',
                    'big_logo'                    => 'big_logo.png',
                ),
                'submit_restore' => array(
                    'icon'                        => '@/pub/media/xmlconnect/custom/320x480/r_icon.png',
                    'loader_image'                => '@/pub/media/xmlconnect/custom/320x480/r_loader_image.png',
                    'loader_image_i4'             => '@/pub/media/xmlconnect/custom/320x480/r_loader_image_i4.png',
                    'logo'                        => '@/pub/media/xmlconnect/custom/320x480/r_logo.png',
                    'big_logo'                    => '@/pub/media/xmlconnect/custom/320x480/r_big_logo.png',
                    'ipad_loader_portrait_image'  => '@/pub/media/xmlconnect/custom/320x480/r_ipad_portrait_image.png',
                    'ipad_loader_landscape_image' => '@/pub/media/xmlconnect/custom/320x480/r_ipad_landscape_image.png',
                    'ipad_logo'                   => '@/pub/media/xmlconnect/custom/320x480/r_ipad_logo.png',
                ),
            )
        ));

        $params = $model->prepareSubmitParams(array('conf' => array('submit_text' => array(
            'key'         => 'Application Key',
            'title'       => 'Application Title',
            'description' => 'Application Description',
            'email'       => 'author@example.com',
            'price_free'  => '1',
            'country'     => array('AR', 'DE', 'MG', 'RU', 'NEW_COUNTRIES', 'FR', 'MK', 'SA'),
            'copyright'   => 'c',
            'keywords'    => 'key, title, author',
        ))));

        $this->assertEquals($params, $model->getSubmitParams());
        $this->assertArrayHasKey('magentoversion', $params);
        unset($params['magentoversion']);
        $this->assertEquals(array(
            'key' => 'Application Key',
            'title' => 'Application Title',
            'description' => 'Application Description',
            'email' => 'author@example.com',
            'price_free' => '1',
            'country' => 'AR,DE,MG,RU,NEW_COUNTRIES,FR,MK,SA',
            'copyright' => 'c',
            'keywords' => 'key, title, author',
            'name' => 'Application Name',
            'code' => 'defipa1',
            'type' => 'ipad',
            'url' => 'http://localhost/index.php/xmlconnect/configuration/index/app_code/defipa1/',
            'icon' => '@/pub/media/xmlconnect/custom/320x480/icon.png',
            'ipad_loader_portrait_image' => '@/pub/media/xmlconnect/custom/320x480/r_ipad_portrait_image.png',
            'ipad_loader_landscape_image' => '@/pub/media/xmlconnect/custom/320x480/r_ipad_landscape_image.png',
            'ipad_logo' => '@/pub/media/xmlconnect/custom/320x480/r_ipad_logo.png',
            'big_logo' => '@/pub/media/xmlconnect/custom/320x480/big_logo.png',
        ), $params);
    }
}
