<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
    protected $_requestMock;

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
        $this->_roleMock = $this->getMock('Magento\AdminGws\Model\Role', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false);
        $this->_objectFactory = $this->getMock('Magento\ObjectManager', array(), array(), '', false);
        $storeManager = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);
        $app = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);

        $this->_controllerMock = $this->getMock('Magento\Adminhtml\Controller\Action', array(), array(), '', false);
        $this->_ctrlRequestMock = $this->getMock(
            'Magento\Core\Controller\Request\Http',
            array(),
            array(),
            '',
            false
        );
        $this->_controllerMock->expects($this->once())
            ->method('getRequest')->will($this->returnValue($this->_ctrlRequestMock));

        $coreRegistry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);

        $this->_model = new \Magento\AdminGws\Model\Controllers(
            $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false),
            $this->getMock('Magento\Backend\Model\Session', array(), array(), '', false),
            $this->getMock('Magento\AdminGws\Model\Resource\CollectionsFactory', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Resource\ProductFactory', array(), array(), '', false),
            $this->_roleMock,
            $coreRegistry,
            $this->_requestMock,
            $this->_objectFactory,
            $storeManager,
            $app
        );
    }

    protected function tearDown()
    {
        unset($this->_controllerMock);
        unset($this->_ctrlRequestMock);
        unset($this->_model);
        unset($this->_objectFactory);
        unset($this->_requestMock);
        unset($this->_roleMock);
    }

    /**
     * Test deny access if role has no allowed website ids and there are considering actions to deny
     */
    public function testValidateRuleEntityActionRoleHasntWebSiteIdsAndConsideringActionsToDenyForwardAvoidCycling()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));

        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('denied'));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(null));

        $this->_model->validateRuleEntityAction($this->_controllerMock);
    }

    /**
     * Test deny access if role has no allowed website ids and there are considering actions to deny
     */
    public function testValidateRuleEntityActionRoleHasntWebSiteIdsAndConsideringActionsToDenyForward()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));

        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('any_action'));
        $this->_requestMock->expects($this->once())->method('initForward');
        $this->_requestMock->expects($this->once())->method('setActionName')
            ->with($this->equalTo('denied'))->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setDispatched')->with($this->equalTo(false));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(null));

        $this->_model->validateRuleEntityAction($this->_controllerMock);
    }

    /**
     * Test stop further validating if role has any allowed website ids and
     * there are considering any action which is not in deny list
     */
    public function testValidateRuleEntityActionWhichIsNotInDenyList()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('any_action'));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(null));
        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /**
     * Test stop further validating if there is no an appropriate entity id in request params
     */
    public function testValidateRuleEntityActionNoAppropriateEntityIdInRequestParams()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(null));
        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));
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
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getControllerName')->will($this->returnValue($controllerName));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(1));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));

        $this->_objectFactory->expects($this->once())
            ->method('create')->with($this->equalTo($modelName))->will($this->returnValue(null));

        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    public function validateRuleEntityActionGetValidModuleClassNameDataProvider()
    {
        return array(
            array(
                'promo_catalog',
                'Magento\CatalogRule\Model\Rule',
            ),
            array(
                'promo_quote',
                'Magento\SalesRule\Model\Rule'
            ),
            array(
                'reminder',
                'Magento\Reminder\Model\Rule'
            ),
            array(
                'customersegment',
                'Magento\CustomerSegment\Model\Segment'
            ),
        );
    }

    /*
     * Test get entity model class name invalid controller name
     */
    public function testValidateRuleEntityActionGetModuleClassNameWithInvalidController()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects($this->once())
            ->method('getControllerName')->will($this->returnValue('some_other'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(1));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));

        $this->_objectFactory->expects($this->exactly(0))->method('create');

        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /*
     * Test deny action if specified rule entity doesn't exist
     */
    public function testValidateRuleEntityActionDenyActionIfSpecifiedRuleEntityDoesntExist()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getControllerName')->will($this->returnValue('promo_catalog'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(1));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));

        $modelMock = $this->getMock('Magento\CatalogRule\Model\Rule', array(), array(), '', false);
        $modelMock->expects($this->once())->method('load')->with(1);
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(false));

        $this->_objectFactory->expects($this->exactly(1))
            ->method('create')->will($this->returnValue($modelMock));

        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('denied'));

        $this->assertEmpty($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /*
     * Test deny actions what lead to changing data if role has no exclusive access to assigned to rule entity websites
     */
    public function testValidateRuleEntityActionDenyActionIfRoleHasNoExclusiveAccessToAssignedToRuleEntityWebsites()
    {
        $modelMock = $this->getMock('Magento\CatalogRule\Model\Rule', array(), array(), '', false);

        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects($this->once())
            ->method('getControllerName')->will($this->returnValue('promo_catalog'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(array(1)));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));
        $this->_roleMock->expects($this->once())
            ->method('hasExclusiveAccess')->with($this->equalTo(array(0 => 1, 2 => 2)))
            ->will($this->returnValue(false));

        $this->_objectFactory->expects($this->exactly(1))
            ->method('create')->will($this->returnValue($modelMock));

        $modelMock->expects($this->once())->method('load')->with(array(1));
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $modelMock->expects($this->once())->method('getOrigData')->will($this->returnValue(array(1, 2)));

        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('denied'));

        $this->assertEmpty($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /*
     * Test deny action if role has no access to assigned to rule entity websites
     */
    public function testValidateRuleEntityActionDenyActionIfRoleHasNoAccessToAssignedToRuleEntityWebsites()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock
            ->expects($this->any())->method('getParam')->will($this->returnValue(array(1)));
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getControllerName')->will($this->returnValue('promo_catalog'));

        $modelMock = $this->getMock('Magento\CatalogRule\Model\Rule', array(), array(), '', false);
        $modelMock->expects($this->once())->method('load')->with(array(1));
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $modelMock->expects($this->once())->method('getOrigData')->will($this->returnValue(array(1, 2)));

        $this->_objectFactory->expects($this->exactly(1))
            ->method('create')->will($this->returnValue($modelMock));
        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));

        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('denied'));

        $this->_roleMock->expects($this->once())
            ->method('hasExclusiveAccess')->with($this->equalTo(array(0 => 1, 2 => 2)))
            ->will($this->returnValue(true));

        $this->_roleMock->expects($this->once())
            ->method('hasWebsiteAccess')->with($this->equalTo(array(0 => 1, 2 => 2)))
            ->will($this->returnValue(false));

        $this->assertEmpty($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /*
     * Test validate rule entity action with valid params
     */
    public function testValidateRuleEntityActionWithValidParams()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getControllerName')->will($this->returnValue('promo_catalog'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(array(1)));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));

        $modelMock = $this->getMock('Magento\CatalogRule\Model\Rule', array(), array(), '', false);
        $modelMock->expects($this->once())->method('load')->with(array(1));
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $modelMock->expects($this->once())->method('getOrigData')->will($this->returnValue(array(1, 2)));

        $this->_objectFactory->expects($this->exactly(1))
            ->method('create')->will($this->returnValue($modelMock));

        $this->_roleMock->expects($this->once())
            ->method('hasExclusiveAccess')->with($this->equalTo(array(0 => 1, 2 => 2)))
            ->will($this->returnValue(true));

        $this->_roleMock->expects($this->once())
            ->method('hasWebsiteAccess')->with($this->equalTo(array(0 => 1, 2 => 2)))
            ->will($this->returnValue(true));

        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }
}
