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
 * @category   Tests
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../../PHPUnitTestInit.php';
    PHPUnitTestInit::runMe(__FILE__);
}

class Enterprise_Invitation_Model_InvitationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    private $_defaultStoreId;
    /**
     * @var int
     */
    private $_defaultGroupId;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_defaultStoreId = Mage::app()->getAnyStoreView()->getId();
        $this->_defaultGroupId = Mage::getSingleton('customer/customer')->setStoreId($this->_defaultStoreId)->getGroupId();
    }

    /**
     * Test invitation creation and sending right after that
     *
     * @see bug #14285
     */
    public function testInvitationCreateAndSend()
    {
        Mage::getSingleton('enterprise_invitation/invitation')->getResource()->beginTransaction();
        Mage::mockFactory('model', 'core/email_template', $this, array('send'));
        Mage::$factoryMocks['model']['core/email_template']['expects'] = $this->once();
        Mage::$factoryMocks['model']['core/email_template']['method'] = 'send';

        $invitation = Mage::getModel('enterprise_invitation/invitation')
            ->setData(array(
                'email'    => microtime(true) . '@example.com',
                'store_id' => $this->_defaultStoreId,
                'message'  => md5('microtime'),
                'group_id' => $this->_defaultGroupId,
            ))->save();
        $invitation->sendInvitationEmail();

        Mage::unmockFactory('model', 'core/email_template');
        Mage::getSingleton('enterprise_invitation/invitation')->getResource()->rollBack();
    }
}
