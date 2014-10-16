<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Widget\Grid\Row;

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUrl()
    {
        $itemId = 3;
        $urlPath = 'mng/item/edit';

        $itemMock = $this->getMock('Magento\Framework\Object', array('getItemId'), array(), '', false);
        $itemMock->expects($this->once())->method('getItemId')->will($this->returnValue($itemId));

        $urlModelMock = $this->getMock(
            'Magento\Backend\Model\Url',
            array(),
            array(),
            '',
            false
        );
        $urlModelMock->expects(
            $this->once()
        )->method(
            'getUrl'
        )->will(
            $this->returnValue('http://localhost/' . $urlPath . '/flag/1/item_id/' . $itemId)
        );

        $model = new \Magento\Backend\Model\Widget\Grid\Row\UrlGenerator(
            $urlModelMock,
            array(
                'path' => $urlPath,
                'params' => array('flag' => 1),
                'extraParamsTemplate' => array('item_id' => 'getItemId')
            )
        );

        $url = $model->getUrl($itemMock);

        $this->assertContains($urlPath, $url);
        $this->assertContains('flag/1', $url);
        $this->assertContains('item_id/' . $itemId, $url);
    }
}
