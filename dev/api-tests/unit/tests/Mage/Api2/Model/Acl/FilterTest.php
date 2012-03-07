<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test API ACL filter
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl_FilterTest extends Mage_PHPUnit_TestCase
{
    /**
     * Test get allowed for
     * multiAction (Mage_Api2_Model_Resource::OPERATION_UPDATE, Mage_Api2_Model_Resource::OPERATION_DELETE)
     */
    public function testGetAllowedAttributesForMultiAction()
    {
        $allowedAttrs = array('attr1', 'attr2', 'attr2');
        $idFieldName = 'item_id_test';
        $userType = Mage_Api2_Model_Auth_User_Customer::USER_TYPE;
        $resourceType = 'test_resource';
        $operationType = Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_WRITE;
        $operation = Mage_Api2_Model_Resource::OPERATION_UPDATE;

        $helperMock = $this->getHelperMockBuilder('api2')
            ->setMethods(array('isAllAttributesAllowed', 'getAllowedAttributes'))
            ->getMock();

        $helperMock->expects($this->any())
            ->method('isAllAttributesAllowed')
            ->will($this->returnValue(false));

        $helperMock->expects($this->any())
            ->method('getAllowedAttributes')
            ->with($userType, $resourceType, $operationType)
            ->will($this->returnValue($allowedAttrs));

        $resourceMock = $this->getMockForAbstractClass('Mage_Api2_Model_Resource', array(), '', false, true, true,
            array('getUserType', 'getResourceType', 'getOperation', 'getIdFieldName'));

        $resourceMock->expects($this->any())
            ->method('getUserType')
            ->will($this->returnValue($userType));

        $resourceMock->expects($this->any())
            ->method('getResourceType')
            ->will($this->returnValue($resourceType));

        $resourceMock->expects($this->any())
            ->method('getOperation')
            ->will($this->returnValue($operation));

        $resourceMock->expects($this->any())
            ->method('getIdFieldName')
            ->will($this->returnValue($idFieldName));

        $allowedAttrsForMultiactions = Mage::getModel('api2/acl_filter', $resourceMock)
            ->getAllowedAttributes($operationType);

        $this->assertCount(count($allowedAttrs) + 1, $allowedAttrsForMultiactions);
        $this->assertContains($idFieldName, $allowedAttrsForMultiactions);
    }
}
