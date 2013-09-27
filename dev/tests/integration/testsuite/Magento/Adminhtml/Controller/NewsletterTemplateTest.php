<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Controller_NewsletterTemplateTest extends Magento_Backend_Utility_Controller
{
    /**
     * @var Magento_Newsletter_Model_Template
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $post = array('code'=>'test data',
                      'subject'=>'test data2',
                      'sender_email'=>'sender@email.com',
                      'sender_name'=>'Test Sender Name',
                      'text'=>'Template Content');
        $this->getRequest()->setPost($post);
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Newsletter_Model_Template');
    }

    protected function tearDown()
    {
        /**
         * Unset messages
         */
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Backend_Model_Session')
            ->getMessages(true);
        unset($this->_model);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testSaveActionCreateNewTemplateAndVerifySuccessMessage()
    {
        $this->_model->loadByCode('some_unique_code');
        $this->getRequest()->setParam('id', $this->_model->getId());
        $this->dispatch('backend/admin/newsletter_template/save');
        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), Magento_Core_Model_Message::ERROR);
        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The newsletter template has been saved.')), Magento_Core_Model_Message::SUCCESS
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/newsletter_sample.php
     */
    public function testSaveActionEditTemplateAndVerifySuccessMessage()
    {
        $this->_model->loadByCode('some_unique_code');
        $this->getRequest()->setParam('id', $this->_model->getId());
        $this->dispatch('backend/admin/newsletter_template/save');

        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), Magento_Core_Model_Message::ERROR);

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The newsletter template has been saved.')), Magento_Core_Model_Message::SUCCESS
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testSaveActionTemplateWithInvalidDataAndVerifySuccessMessage()
    {
        $post = array('code'=>'test data',
                      'subject'=>'test data2',
                      'sender_email'=>'sender_email.com',
                      'sender_name'=>'Test Sender Name',
                      'text'=>'Template Content');
        $this->getRequest()->setPost($post);
        $this->dispatch('backend/admin/newsletter_template/save');

        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), Magento_Core_Model_Message::ERROR);

        /**
         * Check that success message is not set
         */
        $this->assertSessionMessages($this->isEmpty(), Magento_Core_Model_Message::SUCCESS);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/newsletter_sample.php
     */
    public function testDeleteActionTemplateAndVerifySuccessMessage()
    {
        $this->_model->loadByCode('some_unique_code');
        $this->getRequest()->setParam('id', $this->_model->getId());
        $this->dispatch('backend/admin/newsletter_template/delete');

        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), Magento_Core_Model_Message::ERROR);

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The newsletter template has been deleted.')), Magento_Core_Model_Message::SUCCESS
        );
    }
}
