<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Controller;

/**
 * @magentoDbIsolation enabled
 */
class ManageTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Newsletter\Model\Template
     */
    protected $_model;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_coreSession;

    protected function setUp()
    {
        parent::setUp();
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $this->_customerSession->setCustomerId(1);
        $this->_coreSession = $objectManager->get('Magento\Core\Model\Session');
        $this->_coreSession->setData('_form_key', 'formKey');
    }

    protected function tearDown()
    {
        $this->_customerSession->setCustomerId(null);
        $this->_coreSession->unsData('_form_key');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveAction()
    {
        $this->getRequest()
            ->setParam('form_key', 'formKey')
            ->setParam('is_subscribed', '1');
        $this->dispatch('newsletter/manage/save');

        $this->assertRedirect($this->stringContains('customer/account/'));

        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), \Magento\Message\MessageInterface::TYPE_ERROR);

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('We saved the subscription.')),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveActionRemoveSubscription()
    {
        $this->getRequest()
            ->setParam('form_key', 'formKey')
            ->setParam('is_subscribed', '0');
        $this->dispatch('newsletter/manage/save');

        $this->assertRedirect($this->stringContains('customer/account/'));

        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), \Magento\Message\MessageInterface::TYPE_ERROR);

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('We removed the subscription.')),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
    }
}
