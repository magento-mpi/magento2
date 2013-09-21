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

namespace Magento\Adminhtml\Controller;

/**
 * @magentoAppArea adminhtml
 */
class NewsletterQueueTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @var \Magento\Newsletter\Model\Template
     */
    protected $_model;

    public function setUp()
    {
        parent::setUp();
        $this->_model = \Mage::getModel('Magento\Newsletter\Model\Template');
    }
    public function tearDown()
    {
        /**
         * Unset messages
         */
        \Mage::getSingleton('Magento\Backend\Model\Session')->getMessages(true);
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
        $this->assertSessionMessages($this->isEmpty(), \Magento\Core\Model\Message::ERROR);

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The newsletter queue has been saved.')), \Magento\Core\Model\Message::SUCCESS
        );
    }
}
