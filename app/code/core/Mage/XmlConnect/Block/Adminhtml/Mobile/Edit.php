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
 * Application edit block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Setting app action buttons for application
     */
    protected function _construct()
    {
        $this->_objectId    = 'application_id';
        $this->_controller  = 'adminhtml_mobile';
        $this->_blockGroup  = 'Mage_XmlConnect';
        parent::_construct();
        if ((bool)!Mage::getSingleton('Mage_Adminhtml_Model_Session')->getNewApplication()) {
            try {
                $app = Mage::helper('Mage_XmlConnect_Helper_Data')->getApplication();
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                return;
            }

            $this->_updateButton('save', 'label', $this->__('Save'));
            $this->_updateButton('save', 'onclick', 'if (editForm.submit()) {disableElements(\'save\')}');

            $this->_addButton('save_and_continue', array(
                'label'     => $this->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save',
            ), -5);

            if ($app->getId()) {
                $this->_addButton('submit_application_button', array(
                    'label' =>  $this->__('Save and Submit App'),
                    'onclick'    => 'saveAndSubmitApp()',
                    'class'     => 'save'
                ), -10);
            }

            $this->_formScripts[] = 'function saveAndContinueEdit() {'
                .'if (editForm.submit($(\'edit_form\').action + \'back/edit/\')) {disableElements(\'save\')};}';
            if ($app->getId()) {
                $this->_formScripts[] = 'function saveAndSubmitApp() {'
                    .'if (editForm.submit($(\'edit_form\').action + \'submitapp/' . $app->getId() . '\')) {'
                    .'disableElements(\'save\')};}';
            }
        } else {
            $this->removeButton('save');
            $this->removeButton('delete');
        }

        if (isset($app) && $app->getIsSubmitted()) {
            $this->removeButton('delete');
        }
        $this->removeButton('reset');
    }

    /**
     * Adding JS scripts and styles to block
     *
     * @throws Mage_Core_Exception
     * @return Mage_Adminhtml_Block_Widget_Form_Container
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addJs('jscolor/jscolor.js');
        $this->getLayout()->getBlock('head')->addJs('scriptaculous/scriptaculous.js');

        if ((bool)!Mage::getSingleton('Mage_Adminhtml_Model_Session')->getNewApplication()) {
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
        if ((bool)!Mage::getSingleton('Mage_Adminhtml_Model_Session')->getNewApplication()) {
            $app = Mage::helper('Mage_XmlConnect_Helper_Data')->getApplication();
        }

        if (isset($app) && $app->getId()) {
            return $this->__('Edit App "%s"', $this->escapeHtml($app->getName()));
        } else {
            return $this->__('New App');
        }
    }
}
