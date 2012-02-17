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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test deleting REST Role from Backend
 *
 * @method RestRoles_Helper restRolesHelper()
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RestRoles_DeleteTest extends Mage_Selenium_TestCase
{
     /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

     /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Web Services -> REST Roles.</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_rest_roles');
    }

     /**
     * <p>Test delete Rest Role.</p>
     * <p>Steps:</p>
     * <p>1. Click Felete Role button.</p>
     * <p>2. Verify that 'Rest Roles' page is opened.</p>
     * <p>3. Verify that 'Role has been deleted' message is showed.</p>
     * <p>4. Verify that there is no deleted role in the grid.
     *
     * @test
     */
    public function deleteRestRole()
    {
        //preconditions
        $restRoleData = $this->loadData('generic_rest_role');
        $this->restRolesHelper()->createRestRole($restRoleData);
        //steps
        $this->restRolesHelper()->deleteRestRole($restRoleData['rest_role_name']);
        //verifying
        $this->assertTrue($this->checkCurrentPage('manage_rest_roles'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_delete_rest_role');
        $this->assertNull($this->search(array('role_name' => $restRoleData['rest_role_name']), 'rest_role_list'),
            'Role is still present in the grid');
    }
}
