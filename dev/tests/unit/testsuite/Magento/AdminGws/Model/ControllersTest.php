<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdminGws\Model;

class ControllersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Controllers
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_roleMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * Controller request object
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ctrlRequestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_controllerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactory;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_roleMock = $this->getMock('Magento\AdminGws\Model\Role', [], [], '', false);
        $this->_objectFactory = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->_storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false);
        $response = $this->getMock('Magento\Framework\App\ResponseInterface', [], [], '', false);

        $this->_controllerMock = $this->getMock('\Magento\Backend\App\Action', [], [], '', false);
        $this->_ctrlRequestMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);

        $coreRegistry = $this->getMock('Magento\Framework\Registry', [], [], '', false);

        $this->_model = $helper->getObject(
            'Magento\AdminGws\Model\Controllers',
            [
                'role' => $this->_roleMock,
                'registry' => $coreRegistry,
                'objectManager' => $this->_objectFactory,
                'storeManager' => $this->_storeManagerMock,
                'response' => $response,
                'request' => $this->_ctrlRequestMock
            ]
        );
    }

    protected function tearDown()
    {
        unset($this->_controllerMock);
        unset($this->_ctrlRequestMock);
        unset($this->_model);
        unset($this->_objectFactory);
        unset($this->_roleMock);
    }

    /**
     * Test deny access if role has no allowed website ids and there are considering actions to deny
     */
    public function testValidateRuleEntityActionRoleHasntWebSiteIdsAndConsideringActionsToDenyForwardAvoidCycling()
    {
        $this->_ctrlRequestMock->expects($this->at(0))->method('getActionName')->will($this->returnValue('edit'));

        $this->_ctrlRequestMock->expects($this->at(1))->method('getActionName')->will($this->returnValue('denied'));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(null));

        $this->_model->validateRuleEntityAction();
    }

    /**
     * Test deny access if role has no allowed website ids and there are considering actions to deny
     */
    public function testValidateRuleEntityActionRoleHasntWebSiteIdsAndConsideringActionsToDenyForward()
    {
        $this->_ctrlRequestMock->expects($this->at(0))->method('getActionName')->will($this->returnValue('edit'));

        $this->_ctrlRequestMock->expects(
            $this->at(1)
        )->method(
            'getActionName'
        )->will(
            $this->returnValue('any_action')
        );
        $this->_ctrlRequestMock->expects($this->once())->method('initForward');
        $this->_ctrlRequestMock->expects(
            $this->once()
        )->method(
            'setActionName'
        )->with(
            $this->equalTo('denied')
        )->will(
            $this->returnSelf()
        );
        $this->_ctrlRequestMock->expects($this->once())->method('setDispatched')->with($this->equalTo(false));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(null));

        $this->_model->validateRuleEntityAction();
    }

    /**
     * Test stop further validating if role has any allowed website ids and
     * there are considering any action which is not in deny list
     */
    public function testValidateRuleEntityActionWhichIsNotInDenyList()
    {
        $this->_ctrlRequestMock->expects(
            $this->once()
        )->method(
            'getActionName'
        )->will(
            $this->returnValue('any_action')
        );

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(null));
        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /**
     * Test stop further validating if there is no an appropriate entity id in request params
     */
    public function testValidateRuleEntityActionNoAppropriateEntityIdInRequestParams()
    {
        $this->_ctrlRequestMock->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(null));
        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue([1]));
        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /**
     * Test get valid entity model class name
     * @param $controllerName string
     * @param $modelName string
     * @dataProvider validateRuleEntityActionGetValidModuleClassNameDataProvider
     */
    public function testValidateRuleEntityActionGetValidModuleClassName($controllerName, $modelName)
    {
        $this->_ctrlRequestMock->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects(
            $this->once()
        )->method(
            'getControllerName'
        )->will(
            $this->returnValue($controllerName)
        );
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(1));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue([1]));

        $this->_objectFactory->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $this->equalTo($modelName)
        )->will(
            $this->returnValue(null)
        );

        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    public function validateRuleEntityActionGetValidModuleClassNameDataProvider()
    {
        return [
            ['promo_catalog', 'Magento\CatalogRule\Model\Rule'],
            ['promo_quote', 'Magento\SalesRule\Model\Rule'],
            ['reminder', 'Magento\Reminder\Model\Rule'],
            ['customersegment', 'Magento\CustomerSegment\Model\Segment']
        ];
    }

    /*
     * Test get entity model class name invalid controller name
     */
    public function testValidateRuleEntityActionGetModuleClassNameWithInvalidController()
    {
        $this->_ctrlRequestMock->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects(
            $this->once()
        )->method(
            'getControllerName'
        )->will(
            $this->returnValue('some_other')
        );
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(1));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue([1]));

        $this->_objectFactory->expects($this->exactly(0))->method('create');

        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /*
     * Test deny action if specified rule entity doesn't exist
     */
    public function testValidateRuleEntityActionDenyActionIfSpecifiedRuleEntityDoesntExist()
    {
        $this->_ctrlRequestMock->expects($this->at(0))->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects(
            $this->once()
        )->method(
            'getControllerName'
        )->will(
            $this->returnValue('promo_catalog')
        );
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(1));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue([1]));

        $modelMock = $this->getMock('Magento\CatalogRule\Model\Rule', [], [], '', false);
        $modelMock->expects($this->once())->method('load')->with(1);
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(false));

        $this->_objectFactory->expects($this->exactly(1))->method('create')->will($this->returnValue($modelMock));

        $this->_ctrlRequestMock->expects($this->at(1))->method('getActionName')->will($this->returnValue('denied'));
        $this->_ctrlRequestMock->expects(
            $this->any()
        )->method(
            'setActionName'
        )->will(
            $this->returnValue($this->_ctrlRequestMock)
        );

        $this->assertEmpty($this->_model->validateRuleEntityAction());
    }

    /*
     * Test deny actions what lead to changing data if role has no exclusive access to assigned to rule entity websites
     */
    public function testValidateRuleEntityActionDenyActionIfRoleHasNoExclusiveAccessToAssignedToRuleEntityWebsites()
    {
        $modelMock = $this->getMock('Magento\CatalogRule\Model\Rule', [], [], '', false);

        $this->_ctrlRequestMock->expects($this->at(0))->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects(
            $this->once()
        )->method(
            'getControllerName'
        )->will(
            $this->returnValue('promo_catalog')
        );
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue([1]));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue([1]));
        $this->_roleMock->expects(
            $this->once()
        )->method(
            'hasExclusiveAccess'
        )->with(
            $this->equalTo([0 => 1, 2 => 2])
        )->will(
            $this->returnValue(false)
        );

        $this->_objectFactory->expects($this->exactly(1))->method('create')->will($this->returnValue($modelMock));

        $modelMock->expects($this->once())->method('load')->with([1]);
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $modelMock->expects($this->once())->method('getOrigData')->will($this->returnValue([1, 2]));

        $this->_ctrlRequestMock->expects($this->at(1))->method('getActionName')->will($this->returnValue('denied'));
        $this->_ctrlRequestMock->expects(
            $this->any()
        )->method(
            'setActionName'
        )->will(
            $this->returnValue($this->_ctrlRequestMock)
        );

        $this->assertEmpty($this->_model->validateRuleEntityAction());
    }

    /*
     * Test deny action if role has no access to assigned to rule entity websites
     */
    public function testValidateRuleEntityActionDenyActionIfRoleHasNoAccessToAssignedToRuleEntityWebsites()
    {
        $this->_ctrlRequestMock->expects($this->at(0))->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue([1]));
        $this->_ctrlRequestMock->expects(
            $this->once()
        )->method(
            'getControllerName'
        )->will(
            $this->returnValue('promo_catalog')
        );

        $modelMock = $this->getMock('Magento\CatalogRule\Model\Rule', [], [], '', false);
        $modelMock->expects($this->once())->method('load')->with([1]);
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $modelMock->expects($this->once())->method('getOrigData')->will($this->returnValue([1, 2]));

        $this->_objectFactory->expects($this->exactly(1))->method('create')->will($this->returnValue($modelMock));
        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue([1]));

        $this->_ctrlRequestMock->expects($this->at(1))->method('getActionName')->will($this->returnValue('denied'));

        $this->_ctrlRequestMock->expects(
            $this->any()
        )->method(
            'setActionName'
        )->will(
            $this->returnValue($this->_ctrlRequestMock)
        );

        $this->_roleMock->expects(
            $this->once()
        )->method(
            'hasExclusiveAccess'
        )->with(
            $this->equalTo([0 => 1, 2 => 2])
        )->will(
            $this->returnValue(true)
        );

        $this->_roleMock->expects(
            $this->once()
        )->method(
            'hasWebsiteAccess'
        )->with(
            $this->equalTo([0 => 1, 2 => 2])
        )->will(
            $this->returnValue(false)
        );

        $this->assertEmpty($this->_model->validateRuleEntityAction());
    }

    /**
     * @param array $post
     * @param boolean $result
     * @param boolean $isAll
     *
     * @dataProvider validateCmsHierarchyActionDataProvider
     */
    public function testValidateCmsHierarchyAction(array $post, $isAll, $result)
    {
        $this->_ctrlRequestMock->expects($this->any())
            ->method('getPost')
            ->will($this->returnValue($post));
        $this->_ctrlRequestMock->expects($this->any())
            ->method('setActionName')
            ->will($this->returnSelf());
        $websiteId = (isset($post['website']))? $post['website'] : 1;
        $websiteMock = $this->getMockBuilder('\Magento\Store\Model\Website')
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();
        $websiteMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($websiteId));

        $storeId = (isset($post['store']))? $post['store'] : 1;
        $storeMock = $this->getMockBuilder('\Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getWebsite'])
            ->getMock();
        $storeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($storeId));
        $storeMock->expects($this->any())
            ->method('getWebsite')
            ->will($this->returnValue($websiteMock));

        $this->_storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeMock));

        $hasExclusiveAccess = in_array($websiteMock->getId(), [1]);
        $hasExclusiveStoreAccess = in_array($storeMock->getId(), [2]);

        $this->_roleMock->expects($this->any())
            ->method('hasExclusiveAccess')
            ->will($this->returnValue($hasExclusiveAccess));

        $this->_roleMock->expects($this->any())
            ->method('hasExclusiveStoreAccess')
            ->will($this->returnValue($hasExclusiveStoreAccess));

        $this->_roleMock->expects($this->any())
            ->method('getIsAll')
            ->will($this->returnValue($isAll));

        $this->assertEquals($result, $this->_model->validateCmsHierarchyAction());
    }

    /**
     * Data provider for testValidateCmsHierarchyAction()
     *
     * @return array
     */
    public function validateCmsHierarchyActionDataProvider()
    {
        return [
            [[], true, true],
            [[], false, false],
            [['website' => 1, 'store' => 1], false, false],
            [['store' => 2], false, true],
            [['store' => 1], false, false],
        ];
    }

    /*
     * Test validate rule entity action with valid params
     */
    public function testValidateRuleEntityActionWithValidParams()
    {
        $this->_ctrlRequestMock->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects(
            $this->once()
        )->method(
            'getControllerName'
        )->will(
            $this->returnValue('promo_catalog')
        );
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue([1]));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue([1]));

        $modelMock = $this->getMock('Magento\CatalogRule\Model\Rule', [], [], '', false);
        $modelMock->expects($this->once())->method('load')->with([1]);
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $modelMock->expects($this->once())->method('getOrigData')->will($this->returnValue([1, 2]));

        $this->_objectFactory->expects($this->exactly(1))->method('create')->will($this->returnValue($modelMock));

        $this->_roleMock->expects(
            $this->once()
        )->method(
            'hasExclusiveAccess'
        )->with(
            $this->equalTo([0 => 1, 2 => 2])
        )->will(
            $this->returnValue(true)
        );

        $this->_roleMock->expects(
            $this->once()
        )->method(
            'hasWebsiteAccess'
        )->with(
            $this->equalTo([0 => 1, 2 => 2])
        )->will(
            $this->returnValue(true)
        );

        $this->assertTrue($this->_model->validateRuleEntityAction());
    }
}
