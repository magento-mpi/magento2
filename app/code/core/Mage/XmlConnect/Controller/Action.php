<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect controller abstract
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_XmlConnect_Controller_Action extends Mage_Core_Controller_Front_Action
{
    /**
     * Message status `error`
     */
    const MESSAGE_STATUS_ERROR      = 'error';

    /**
     * Message status `warning`
     */
    const MESSAGE_STATUS_WARNING    = 'warning';

    /**
     * Message status `success`
     */
    const MESSAGE_STATUS_SUCCESS    = 'success';

    /**
     * Message type `alert`
     */
    const MESSAGE_TYPE_ALERT        = 'alert';

    /**
     * Message type `prompt`
     */
    const MESSAGE_TYPE_PROMPT       = 'prompt';

    /**
     * Declare content type header
     * Validate current application
     *
     * @return null
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        /**
         * Load application by specified code and make sure that application exists
         */
        $cookieName = Mage_XmlConnect_Model_Application::APP_CODE_COOKIE_NAME;
        $appCode    = isset($_COOKIE[$cookieName]) ? (string) $_COOKIE[$cookieName] : '';
        $screenSizeCookieName = Mage_XmlConnect_Model_Application::APP_SCREEN_SIZE_NAME;
        $screenSize = isset($_COOKIE[$screenSizeCookieName]) ? (string) $_COOKIE[$screenSizeCookieName] : '';
        if (!$appCode) {
            $this->_message(
                Mage::helper('Mage_XmlConnect_Helper_Data')->__('Specified invalid app code.'), self::MESSAGE_STATUS_ERROR
            );
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
        /**
         * Check is website offline
         */
        if ((int)Mage::getStoreConfig('general/restriction/is_active')
            && (int)Mage::getStoreConfig('general/restriction/mode') == 0
        ) {
            $this->_message(
                Mage::helper('Mage_XmlConnect_Helper_Data')->__('Website is offline.'), self::MESSAGE_STATUS_SUCCESS
            );
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
        /** @var $appModel Mage_XmlConnect_Model_Application */
        $appModel = Mage::getModel('Mage_XmlConnect_Model_Application')->loadByCode($appCode);
        $appModel->setScreenSize($screenSize);
        if ($appModel && $appModel->getId()) {
            Mage::app()->setCurrentStore(
                Mage::app()->getStore($appModel->getStoreId())->getCode()
            );
            Mage::getSingleton('Mage_Core_Model_Locale')->emulate($appModel->getStoreId());
            Mage::register('current_app', $appModel);
        } else {
            $this->_message(
                Mage::helper('Mage_XmlConnect_Helper_Data')->__('Specified invalid app code.'), self::MESSAGE_STATUS_ERROR
            );
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
    }

    /**
     * Validate response body
     *
     * @return null
     */
    public function postDispatch()
    {
        parent::postDispatch();
        $body = $this->getResponse()->getBody();
        if (empty($body)) {
            $this->_message(
                Mage::helper('Mage_XmlConnect_Helper_Data')->__('An error occurred while processing your request.'),
                self::MESSAGE_STATUS_ERROR
            );
        }
    }

    /**
     * Generate message xml and set it to response body
     *
     * @param string $text
     * @param string $status
     * @param array $children
     * @return null
     */
    protected function _message($text, $status, $children = array())
    {
        /** @var $message Mage_XmlConnect_Model_Simplexml_Element */
        $message = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<message></message>'));
        $message->addCustomChild('status', $status);
        $message->addCustomChild('text', $text);

        foreach ($children as $node => $value) {
            $message->addCustomChild($node, $value);
        }

        $this->getResponse()->setBody($message->asNiceXml());
    }
}
