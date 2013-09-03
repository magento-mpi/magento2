<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Contacts
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Contacts index controller
 *
 * @category   Magento
 * @package    Magento_Contacts
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Contacts_Controller_Index extends Magento_Core_Controller_Front_Action
{
    const XML_PATH_EMAIL_RECIPIENT  = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'contacts/email/email_template';
    const XML_PATH_ENABLED          = 'contacts/contacts/enabled';

    /**
     * Check is page enabled
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if( !Mage::getStoreConfigFlag(self::XML_PATH_ENABLED) ) {
            $this->norouteAction();
        }
    }

    /**
     * Show Contact Us page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('contactForm')
            ->setFormAction( Mage::getUrl('*/*/post') );

        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $this->_initLayoutMessages('Magento_Catalog_Model_Session');
        $this->renderLayout();
    }

    /**
     * Post user question
     *
     * @throws Exception
     */
    public function postAction()
    {
        if (!$this->getRequest()->isSecure()) {
            $this->_redirect('*/*/');
            return;
        }
        $post = $this->getRequest()->getPost();
        if ($post) {
            $translate = Mage::getSingleton('Magento_Core_Model_Translate');
            /* @var $translate Magento_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new \Magento\Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                $mailTemplate = Mage::getModel('Magento_Core_Model_Email_Template');
                /* @var $mailTemplate Magento_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array(
                    'area' => Magento_Core_Model_App_Area::AREA_FRONTEND,
                    'store' => Mage::app()->getStore()->getId()
                ))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('Magento_Customer_Model_Session')->addSuccess(__('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.'));
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                Mage::getSingleton('Magento_Customer_Model_Session')->addError(__('Something went wrong submitting your request.'));
                $this->_redirect('*/*/');
                return;
            }

        } else {
            $this->_redirect('*/*/');
        }
    }
}
