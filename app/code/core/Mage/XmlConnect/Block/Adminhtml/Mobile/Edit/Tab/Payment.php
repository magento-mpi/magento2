<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Payment
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_pages;

    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();

        $this->setForm($form);

        $model = Mage::registry('current_app');
        $data = $model->getFormData();

        $fieldset = $form->addFieldset('onepage_checkout', array('legend' => Mage::helper('xmlconnect')->__('Onepage checkout')));

        $fieldset->addField('conf/native/defaultCheckout/isActive', 'select', array(
            'label'     => Mage::helper('xmlconnect')->__('Use Default Checkout method'),
            'name'      => 'conf[native][defaultCheckout][isActive]',
            'options'   => array(
                '1'  => Mage::helper('xmlconnect')->__('Yes'),
                '0' => Mage::helper('xmlconnect')->__('No'),
            ),
            'value'     => (isset($data['conf[native][defaultCheckout][isActive]']) ? $data['conf[native][defaultCheckout][isActive]'] : '1')
        ));

        $paypalActive = (isset($data['conf[native][paypal][isActive]']) ? $data['conf[native][paypal][isActive]'] : '0');

        $fieldset2 = $form->addFieldset('paypal_mep_checkout', array('legend' => Mage::helper('xmlconnect')->__('PayPal MEP')));

        $paypalActiveField = $fieldset2->addField('conf/native/paypal/isActive', 'select', array(
            'label'     => Mage::helper('xmlconnect')->__('Activate paypal checkout'),
            'name'      => 'conf[native][paypal][isActive]',
            'options'   => array(
                '1'  => Mage::helper('xmlconnect')->__('Yes'),
                '0' => Mage::helper('xmlconnect')->__('No'),
            ),
            'value'     => $paypalActive
        ));

        $merchantlabelField = $fieldset2->addField('conf/special/merchantLabel', 'text', array(
            'name'      => 'conf[special][merchantLabel]',
            'label'     => Mage::helper('xmlconnect')->__('Merchant Label'),
            'title'     => Mage::helper('xmlconnect')->__('Merchant Label'),
            'required'  => true,
            'value'     => (isset($data['conf[special][merchantLabel]']) ? $data['conf[special][merchantLabel]'] : '')
        ));

        // field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($merchantlabelField->getHtmlId(), $merchantlabelField->getName())
            ->addFieldMap($paypalActiveField->getHtmlId(), $paypalActiveField->getName())
            ->addFieldDependence(
                $merchantlabelField->getName(),
                $paypalActiveField->getName(),
                1)
        );

        return parent::_prepareForm();
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('xmlconnect')->__('Payment Methods');
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('xmlconnect')->__('Payment Methods');
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
