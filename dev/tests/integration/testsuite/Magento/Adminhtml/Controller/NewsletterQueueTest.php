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
class Magento_Adminhtml_Controller_NewsletterQueueTest extends Magento_Backend_Utility_Controller
{
    /**
     * @var Magento_Newsletter_Model_Template
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('Magento_Newsletter_Model_Template');
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
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/newsletter_sample.php
     * @magentoAppIsolation disabled
     */
    public function testSaveActionQueueTemplateAndVerifySuccessMessage()
    {
        $postForQueue = array('sender_email'=>'johndoe_gieee@unknown-domain.com',
                              'sender_name'=>'john doe',
                              'subject'=>'test subject',
                              'text'=>'newsletter text');
        $this->getRequest()->setPost($postForQueue);
        $this->_model->loadByCode('some_unique_code');
        $this->getRequest()->setParam('template_id', $this->_model->getId());
        $this->dispatch('backend/admin/newsletter_queue/save');

        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), Magento_Core_Model_Message::ERROR);

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The newsletter queue has been saved.')), Magento_Core_Model_Message::SUCCESS
        );
    }
}
