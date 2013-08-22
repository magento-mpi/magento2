<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Widget_Grid_Row_UrlGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testGetUrl()
    {
        $itemId = 3;
        $urlPath = 'mng/item/edit';

        $itemMock = $this->getMock('Magento_Object', array('getItemId'), array(), '', false);
        $itemMock->expects($this->once())
            ->method('getItemId')
            ->will($this->returnValue($itemId));

        $urlModelMock = $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false);
        $urlModelMock->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('http://localhost/' . $urlPath . '/flag/1/item_id/' . $itemId));

        $model = new Magento_Backend_Model_Widget_Grid_Row_UrlGenerator(array(
            'urlModel' => $urlModelMock,
            'path' => $urlPath,
            'params' => array('flag' => 1),
            'extraParamsTemplate' => array('item_id' => 'getItemId')
        ));

        $url = $model->getUrl($itemMock);

        $this->assertContains($urlPath, $url);
        $this->assertContains('flag/1', $url);
        $this->assertContains('item_id/' . $itemId, $url);
    }
}
