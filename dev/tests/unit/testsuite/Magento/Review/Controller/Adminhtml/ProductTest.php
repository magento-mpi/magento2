<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Review\Controller\Adminhtml;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Unit test for \Magento\Review\Controller\Adminhtml\Product
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Review\Controller\Adminhtml\Product
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_messageManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerInterfaceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_reviewModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ratingModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceReviewMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;


    protected function setUp()
    {
        $this->_prepareMockObjects();
        $this->_contextMock->expects($this->any())->method('getRequest')
            ->will($this->returnValue($this->_requestMock));
        $this->_contextMock->expects($this->any())->method('getResponse')
            ->will($this->returnValue($this->_responseMock));
        $this->_contextMock->expects($this->any())->method('getObjectManager')
            ->will($this->returnValue($this->_objectManagerMock));
        $this->_contextMock->expects($this->any())->method('getMessageManager')
            ->will($this->returnValue($this->_messageManagerMock));
        $this->_contextMock->expects($this->any())->method('getHelper')
            ->will($this->returnValue($this->_helperMock));

        $this->_objectManagerHelper = new ObjectManagerHelper($this);
        $this->_model = $this->_objectManagerHelper->getObject(
            'Magento\Review\Controller\Adminhtml\Product',
            [
                'context' => $this->_contextMock,
                'coreRegistry' => $this->_registryMock,
            ]
        );

    }

    /**
     * Get mock objects for SetUp()
     */
    protected function _prepareMockObjects()
    {
        $contextMethods = array(
            'getRequest',
            'getResponse',
            'getObjectManager',
            'getMessageManager',
            'getHelper'
        );
        $requestMethods = array(
            'getPost',
            'getModuleName',
            'setModuleName',
            'getActionName',
            'setActionName',
            'getParam'
        );
        $storeManagerIntMethods = array(
            'hasSingleStore',
            'setIsSingleStoreModeAllowed',
            'isSingleStoreMode',
            'getStore',
            'getStores',
            'getWebsite',
            'getWebsites',
            'reinitStores',
            'getDefaultStoreView',
            'getGroup',
            'getGroups',
            'clearWebsiteCache',
            'setCurrentStore'
        );
        $this->_contextMock = $this->getMock('Magento\Backend\App\Action\Context', $contextMethods, array(), '', false);
        $this->_registryMock = $this->getMock('Magento\Framework\Registry', array(), array(), '', false);
        $this->_requestMock = $this->getMock('\Magento\Framework\App\Request\Http', $requestMethods, array(), '', false);
        $this->_responseMock = $this->getMock('\Magento\Framework\App\Response\Http', array(), array(), '', false);
        $this->_objectManagerMock = $this->getMock(
            '\Magento\Framework\ObjectManager', array('get', 'create', 'configure'), array(), '', false
        );
        $this->_messageManagerMock = $this->getMock('\Magento\Framework\Message\Manager', array(), array(), '', false);
        $this->_storeManagerInterfaceMock = $this->getMock(
            'Magento\Store\Model\StoreManagerInterface', $storeManagerIntMethods, array(), '', false
        );
        $this->_storeManagerMock = $this->getMock(
            'Magento\Store\Model\StoreManager', array('getStore', 'getId'), array(), '', false
        );
        $this->_storeModelMock = $this->getMock(
            'Magento\Store\Model\Store', array('__wakeup', 'getId'), array(), '', false
        );
        $this->_reviewModelMock = $this->getMock(
            'Magento\Review\Model\Review',
            array('__wakeup', 'create', 'save', 'getId', 'getResource', 'aggregate'),
            array(),
            '',
            false
        );
        $this->_ratingModelMock = $this->getMock(
            'Magento\Review\Model\Rating',
            array('__wakeup', 'setRatingId', 'setReviewId', 'addOptionVote'),
            array(),
            '',
            false);
        $this->_helperMock = $this->getMock('\Magento\Backend\Helper\Data', array(), array(), '', false);
    }

    /**
     * Check postAction method and assert that review model storeId equals null.
     */
    public function testPostAction()
    {
        $this->_requestMock->expects($this->at(0))->method('getParam')
            ->will($this->returnValue(1));
        $this->_requestMock->expects($this->at(2))->method('getParam')
            ->will($this->returnValue(array('1' => '1')));
        $this->_requestMock->expects($this->at(3))->method('getParam')
            ->with('ret')->will($this->returnValue('pending'));
        $this->_requestMock->expects($this->any())->method('getPost')
            ->will($this->returnValue(array('status_id' => 1)));
        $this->_objectManagerMock->expects($this->at(0))->method('get')
            ->with('Magento\Store\Model\StoreManagerInterface')
            ->will($this->returnValue($this->_storeManagerInterfaceMock));
        $this->_objectManagerMock->expects($this->at(1))->method('get')
            ->with('Magento\Store\Model\StoreManager')
            ->will($this->returnValue($this->_storeManagerMock));
        $this->_objectManagerMock->expects($this->at(2))->method('create')
            ->with('Magento\Review\Model\Review')
            ->will($this->returnValue($this->_reviewModelMock));
        $this->_objectManagerMock->expects($this->at(3))->method('create')
            ->with('Magento\Review\Model\Rating')
            ->will($this->returnValue($this->_ratingModelMock));
        $this->_storeManagerInterfaceMock->expects($this->any())->method('hasSingleStore')
            ->will($this->returnValue($this->_storeManagerMock));
        $this->_storeManagerMock->expects($this->any())->method('getStore')
            ->will($this->returnValue($this->_storeModelMock));
        $this->_storeModelMock->expects($this->any())->method('getId')
            ->will($this->returnValue(1));
        $this->_reviewModelMock->expects($this->any())->method('save')
            ->will($this->returnValue($this->_reviewModelMock));
        $this->_reviewModelMock->expects($this->any())->method('getId')
            ->will($this->returnValue(1));
        $this->_reviewModelMock->expects($this->any())->method('aggregate')
            ->will($this->returnValue($this->_reviewModelMock));
        $this->_ratingModelMock->expects($this->any())->method('setRatingId')
            ->will($this->returnValue($this->_ratingModelMock));
        $this->_ratingModelMock->expects($this->any())->method('setReviewId')
            ->will($this->returnValue($this->_ratingModelMock));
        $this->_ratingModelMock->expects($this->any())->method('addOptionVote')
            ->will($this->returnValue($this->_ratingModelMock));
        $this->_helperMock->expects($this->any())->method('geturl')
            ->will($this->returnValue('url'));

        $this->_model->postAction();
        $this->assertEquals(0, $this->_reviewModelMock->getStoreId());
    }

}
