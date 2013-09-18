<?php
/**
 * \Magento\Webhook\Model\Webapi\User\Factory
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Webapi_User_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** Values being sent to user service */
    const VALUE_COMPANY_NAME = 'company name';
    const VALUE_SECRET_VALUE = 'secret_value';
    const VALUE_KEY_VALUE = 'key_value';
    const VALUE_EMAIL = 'email@example.com';

    /** @var  array */
    private $_userContext;

    /** @var  int */
    private $_apiUserId;

    protected function setUp()
    {
        $this->_userContext = array(
            'email'     => self::VALUE_EMAIL,
            'key'       => self::VALUE_KEY_VALUE,
            'secret'    => self::VALUE_SECRET_VALUE,
            'company'   => self::VALUE_COMPANY_NAME
        );
    }

    protected function tearDown()
    {
        /** @var \Magento\Webapi\Model\Acl\User $user */
        $user = Mage::getModel('Magento\Webapi\Model\Acl\User');
        $user->load($this->_apiUserId);
        $user->delete();
    }

    public function testCreate()
    {
        /** @var \Magento\Webhook\Model\Webapi\User\Factory $userFactory */
        $userFactory = Mage::getModel('Magento\Webhook\Model\Webapi\User\Factory');
        $this->_apiUserId = $userFactory->createUser($this->_userContext, array('webhook/create'));

        /** @var \Magento\Webapi\Model\Acl\User $user */
        $user = Mage::getModel('Magento\Webapi\Model\Acl\User');
        $user->load($this->_apiUserId);

        $this->assertEquals(self::VALUE_COMPANY_NAME, $user->getCompanyName());
        $this->assertEquals(self::VALUE_EMAIL, $user->getContactEmail());
        $this->assertEquals(self::VALUE_SECRET_VALUE, $user->getSecret());
        $this->assertEquals(self::VALUE_KEY_VALUE, $user->getApiKey());
        $this->assertNotEquals(0, $user->getRoleId());

        /** @var \Magento\Webapi\Model\Resource\Acl\Rule $ruleResources */
        $ruleResources = Mage::getModel('Magento\Webapi\Model\Resource\Acl\Rule');
        $rules = $ruleResources->getResourceIdsByRole($user->getRoleId());
        $this->assertNotEmpty($rules);
    }

}
