<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Magento_User_Model_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture limitations/admin_account 1
     * @magentoDbIsolation enabled
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Sorry, you are using all the admin users your account allows
     */
    public function testSaveCreateRestriction()
    {
        /** @var Magento_User_Model_User $model */
        $model = Mage::getModel('Magento_User_Model_User');
        $model->setData(array(
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'username'  => 'user2',
            'password'  => 'password1',
            'email'     => 'user@magento.com'
        ));
        $model->save();
    }
}
