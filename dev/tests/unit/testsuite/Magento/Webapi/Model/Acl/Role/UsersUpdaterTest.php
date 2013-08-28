<?php
/**
 * Test class for Magento_Webapi_Model_Acl_Role_UsersUpdater
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Role_UsersUpdaterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_helper;

    /**
     * @var Magento_Backend_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendHelper;

    /**
     * @var Magento_Core_Controller_Request_Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var Magento_Webapi_Model_Resource_Acl_User_Collection|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_backendHelper = $this->getMockBuilder('Magento_Backend_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('prepareFilterString'))
            ->getMock();
        $this->_backendHelper->expects($this->any())->method('prepareFilterString')->will($this->returnArgument(0));

        $this->_request = $this->getMockBuilder('Magento_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_collection = $this->getMockBuilder('Magento_Webapi_Model_Resource_Acl_User_Collection')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @dataProvider updateDataProvider
     * @param int $roleId
     * @param array $filters
     * @param bool $isAjax
     * @param mixed $param
     */
    public function testUpdate($roleId, $filters, $isAjax, $param)
    {
        $this->_request->expects($this->any())->method('getParam')->will($this->returnValueMap(array(
            array('role_id', null, $roleId),
            array('filter', '', $filters),
        )));
        $this->_request->expects($this->any())->method('isAjax')->will($this->returnValue($isAjax));

        if ($param) {
            $this->_collection->expects($this->once())->method('addFieldToFilter')->with('role_id', $param);
        } else {
            $this->_collection->expects($this->never())->method('addFieldToFilter');
        }

        /** @var Magento_Webapi_Model_Acl_Role_UsersUpdater $model */
        $model = $this->_helper->getObject('Magento_Webapi_Model_Acl_Role_UsersUpdater', array(
            'request' => $this->_request,
            'backendHelper' => $this->_backendHelper
        ));
        $this->assertEquals($this->_collection, $model->update($this->_collection));
    }

    /**
     * @return array
     */
    public function updateDataProvider()
    {
        return array(
            'Yes' => array(
                3,
                array('in_role_users' => 1),
                true,
                3
            ),
            'No' => array(
                4,
                array('in_role_users' => 0),
                true,
                array(
                    array('neq' => 4),
                    array('is' => 'NULL')
                )
            ),
            'Any' => array(
                5,
                array(),
                true,
                null
            ),
            'Yes_on_ajax' => array(
                6,
                array(),
                false,
                6
            ),
            'Any_without_role_id' => array(
                null,
                array('in_role_users' => 1),
                true,
                null
            )
        );
    }
}
