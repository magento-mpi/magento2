<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Model\Order\Archive\Grid\Row;

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $_model \Magento\SalesArchive\Model\Order\Archive\Grid\Row\UrlGenerator
     */
    protected $_model;

    /**
     * @var $_authorization \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    /**
     * @var $_urlModel \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    protected function setUp()
    {
        $this->_authorizationMock = $this->getMockBuilder('Magento\Framework\AuthorizationInterface')->getMock();

        $this->_urlModelMock = $this->getMock(
            'Magento\Backend\Model\Url',
            [],
            [],
            '',
            false
        );

        $urlMap = [
            [
                'sales/order/view',
                ['order_id' => null],
                'http://localhost/backend/sales/order/view/order_id/',
            ],
            ['sales/order/view', ['order_id' => 1], 'http://localhost/backend/sales/order/view/order_id/1'],
        ];
        $this->_urlModelMock->expects($this->any())->method('getUrl')->will($this->returnValueMap($urlMap));

        $this->_model = new \Magento\SalesArchive\Model\Order\Archive\Grid\Row\UrlGenerator(
            $this->_urlModelMock,
            $this->_authorizationMock,
            ['path' => 'sales/order/view', 'extraParamsTemplate' => ['order_id' => 'getId']]
        );
    }

    public function testAuthNotAllowed()
    {
        $this->_authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('Magento_SalesArchive::orders')
            ->will($this->returnValue(false));

        $this->assertFalse($this->_model->getUrl(new \Magento\Framework\Object()));
    }

    /**
     * @param $item
     * @param $expectedUrl
     * @dataProvider itemsDataProvider
     */
    public function testAuthAllowed($item, $expectedUrl)
    {
        $this->_authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->with('Magento_SalesArchive::orders')
            ->will($this->returnValue(true));
        $result = $this->_model->getUrl($item);

        $this->assertEquals($expectedUrl, $result);
    }

    public function itemsDataProvider()
    {
        return [
            [new \Magento\Framework\Object(), 'http://localhost/backend/sales/order/view/order_id/'],
            [
                new \Magento\Framework\Object(['id' => 1]),
                'http://localhost/backend/sales/order/view/order_id/1'
            ]
        ];
    }
}
