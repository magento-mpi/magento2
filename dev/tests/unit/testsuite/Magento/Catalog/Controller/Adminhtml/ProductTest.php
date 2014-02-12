<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Controller\Adminhtml;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_model;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_priceProcessor;

    public function setUp()
    {
        $productActionMock = $this->getMock(
            'Magento\Catalog\Model\Product\Action',
            array(), array(), '', false
        );

        $objectManagerMock = $this->getMockForAbstractClass('\Magento\ObjectManager', array(), '', true, true,
            true, array('get'));

        $objectManagerMock->expects($this->any())->method('get')
            ->will($this->returnValue($productActionMock));

        $this->_priceProcessor = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Price\Processor', array(), array(), '', false
        );

        $requestInterfaceMock = $this->getMock(
            'Magento\App\RequestInterface',
            array(), array(), '', false
        );

        $responseInterfaceMock = $this->getMock(
            'Magento\App\ResponseInterface', array('setRedirect', 'sendResponse')
        );

        $managerInterfaceMock = $this->getMock(
            'Magento\Message\ManagerInterface',
            array(), array(), '', false
        );

        $sessionMock = $this->getMock(
            'Magento\Backend\Model\Session',
            array(), array(), '', false
        );

        $actionFlagMock = $this->getMock(
            'Magento\App\ActionFlag',
            array(), array(), '', false
        );

        $helperDataMock = $this->getMock(
            'Magento\Backend\Helper\Data',
            array(), array(), '', false
        );

        $contextMock = $this->getMock(
            'Magento\Backend\App\Action\Context',
            array('getRequest', 'getResponse', 'getObjectManager',
                  'getMessageManager', 'getSession', 'getActionFlag', 'getHelper'), array(), '', false
        );

        $contextMock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($requestInterfaceMock));

        $contextMock->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($responseInterfaceMock));

        $contextMock->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManagerMock));

        $contextMock->expects($this->any())
            ->method('getMessageManager')
            ->will($this->returnValue($managerInterfaceMock));

        $contextMock->expects($this->any())
            ->method('getSession')
            ->will($this->returnValue($sessionMock));

        $contextMock->expects($this->any())
            ->method('getActionFlag')
            ->will($this->returnValue($actionFlagMock));

        $contextMock->expects($this->any())
            ->method('getHelper')
            ->will($this->returnValue($helperDataMock));

        $this->_model = new \Magento\Catalog\Controller\Adminhtml\Product(
            $contextMock,
            $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false),
            $this->getMock('Magento\Core\Filter\Date', array(), array(), '', false),
            $this->getMock(
                'Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper',
                array(), array(), '', false
            ),
            $this->getMock(
                'Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter',
                array(), array(), '', false
            ),
            $this->getMock('Magento\Catalog\Model\Product\Copier', array(), array(), '', false),
            $this->_priceProcessor
        );
    }

    public function testMassStatusAction()
    {
        $this->_priceProcessor->expects($this->once())
            ->method('reindexList');

        $this->_model->massStatusAction();
    }
}
