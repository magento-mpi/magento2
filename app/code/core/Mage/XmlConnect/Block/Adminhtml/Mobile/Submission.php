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
 * Application submission block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Submission
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Setting buttons for submit application page
     */
    protected function _construct()
    {
        $this->_objectId    = 'application_id';
        $this->_controller  = 'adminhtml_mobile';
        $this->_blockGroup  = 'Mage_XmlConnect';
        $this->_mode = 'submission';
        parent::_construct();

        $this->removeButton('delete');
        $this->removeButton('save');
        $this->removeButton('reset');

        try {
            $app = Mage::helper('Mage_XmlConnect_Helper_Data')->getApplication();
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return;
        }

        if ($app && $app->getIsResubmitAction()) {
            $label = $this->__('Resubmit App');
        } else {
            $label = $this->__('Submit App');
        }

        $this->_addButton('submission_post', array(
            'class' => 'save',
            'label' => $label,
            'onclick' => "submitApplication()",
        ));

        $this->_updateButton('back', 'label', $this->__('Back to App Edit'));
        $this->_updateButton(
            'back',
            'onclick',
            'setLocation(\'' . $this->getUrl('*/*/edit', array('application_id' => $app->getId())) . '\')'
        );
    }

    /**
     * Adding styles to block
     *
     * @throws Mage_Core_Exception
     * @return Mage_Adminhtml_Block_Widget_Form_Container
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addJs('jscolor/jscolor.js');
        $this->getLayout()->getBlock('head')->addJs('scriptaculous/scriptaculous.js');


        $deviceType = Mage::helper('Mage_XmlConnect_Helper_Data')->getDeviceType();
        switch ($deviceType) {
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE:
                $this->getLayout()->getBlock('head')->addCss('Mage_XmlConnect::css/mobile-home.css');
                $this->getLayout()->getBlock('head')->addCss('Mage_XmlConnect::css/mobile-catalog.css');
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD:
                $this->getLayout()->getBlock('head')->addCss('Mage_XmlConnect::css/mobile-ipad-home.css');
                $this->getLayout()->getBlock('head')->addCss('Mage_XmlConnect::css/mobile-ipad-catalog.css');
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID:
                $this->getLayout()->getBlock('head')->addCss('Mage_XmlConnect::css/mobile-android.css');
                break;
            default:
                Mage::throwException(
                    $this->__('Device doesn\'t recognized: "%s". Unable to load preview model.', $deviceType)
                );
                break;
        }

        return parent::_prepareLayout();
    }

    /**
     * Get form header title
     *
     * @return string
     */
    public function getHeaderText()
    {
        $app = Mage::helper('Mage_XmlConnect_Helper_Data')->getApplication();
        if ($app && $app->getId()) {
            return $this->__('Submit App "%s"', $this->escapeHtml($app->getName()));
        }
        return '';
    }
}
