<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
        $this->markTestSkipped(
            'Bug with phpunit 3.7: PHPUnit_Framework_Exception: Class "%s" already exists'
        );
        $this->_authorizationMock = $this->getMockBuilder('Magento\AuthorizationInterface')
            ->getMock();

        $this->_urlModelMock = $this->getMock('Magento\Backend\Model\Url', array(), array(),
            'Magento\Backend\Model\UrlProxy', false);

        $urlMap = array(
            array(
                'sales/order/view',
                array(
                    'order_id' => null
                ),
                'http://localhost/backend/sales/order/view/order_id/'
            ),
            array(
                'sales/order/view',
                array(
                    'order_id' => 1
                ),
                'http://localhost/backend/sales/order/view/order_id/1'
            ),
        );
        $this->_urlModelMock->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValueMap($urlMap));

        $this->_model = new \Magento\SalesArchive\Model\Order\Archive\Grid\Row\UrlGenerator(
            $this->_urlModelMock,
            $this->_authorizationMock,
            array(
                'path' => '*/sales_order/view',
                'extraParamsTemplate' => array(
                    'order_id' => 'getId'
                )
            )
        );
    }

    public function testAuthNotAllowed()
    {
        $this->_authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('Magento_SalesArchive::orders')
            ->will($this->returnValue(false));

        $this->assertFalse($this->_model->getUrl(new \Magento\Object()));
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

        $this->assertEquals($expectedUrl, $this->_model->getUrl($item));
    }

    public function itemsDataProvider()
    {
        return array(
            array(
                new \Magento\Object(),
                'http://localhost/backend/admin/sales_order/view/order_id/'
            ),
            array(
                new \Magento\Object(
                    array(
                        'id' => 1
                    )
                ),
                'http://localhost/backend/admin/sales_order/view/order_id/1'
            )
        );
    }
}
