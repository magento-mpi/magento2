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

class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Submission
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }


    protected function _prepareLayout()
    {
        $application = Mage::registry('current_app');
        if ($application->getIsResubmitAction()) {
            $block = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('xmlconnect/resubmit.phtml')
                ->setActionUrl($this->getUrl('*/*/editPost', array('key' => $application->getId())))
                ->setActivationKey($application->getActivationKey())
                ->setResubmissionName('conf[submit_text][resubmission_activation_key]');
            $this->setChild('resubmit', $block);

            $block = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('xmlconnect/images.phtml')
                ->setImages($application->getImages());
            $this->setChild('images', $block);
        }
        $this->setChild('payment_disabled', $this->getLayout()->createBlock('adminhtml/template')
            ->setTabsPrefix(Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tabs::XMLCONNECT_MOBILE_APP_TABS_ID)
            ->setTemplate('xmlconnect/payment_disabled.phtml'));

        parent::_prepareLayout();
    }


    /**
     * Add image uploader to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     */
    protected function addImage($fieldset, $fieldName, $title, $note = '')
    {
        $fieldset->addField($fieldName, 'image', array(
            'name'      => $fieldName,
            'label'     => $this->__($title),
            'note'      => !empty($note) ? $note : null,
        ));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $application = Mage::registry('current_app');
        $isResubmit = $application->getIsResubmitAction();
        $formData = $application->getFormData();

        $fieldset = $form->addFieldset('submit0', array('legend' => $this->__('Submission Fields')));

        $fieldset->addField('conf/submit_text/title', 'text', array(
            'name'      => 'conf[submit_text][title]',
            'label'     => $this->__('Title'),
            'maxlength' => '200',
            'value'     => isset($formData['conf[submit_text][title]']) ? $formData['conf[submit_text][title]'] : null,
            'note'      => $this->__('This is the name that will appear beneath your app when users install it to their device.  .  We recommend choosing a name that is 10-12 characters in length, and that your customers will recognize.'),
//            'required'  => true,
        ));

        $field = $fieldset->addField('conf/submit_text/description', 'textarea', array(
            'name'      => 'conf[submit_text][description]',
            'label'     => $this->__('Description'),
            'maxlength' => '500',
            'value'     => isset($formData['conf[submit_text][description]']) ? $formData['conf[submit_text][description]'] : null,
            'note'      => $this->__('This is the description that will appear in the iTunes marketplace. '),
//            'required'  => true,
        ));
        $field->setRows(15);

        $fieldset->addField('conf/submit_text/username/', 'text', array(
            'name'      => 'conf[submit_text][username]',
            'label'     => $this->__('Username'),
            'maxlength' => '40',
            'value'     => isset($formData['conf[submit_text][username]']) ? $formData['conf[submit_text][username]'] : null,
            'note'      => $this->__('Paypal Merchant Account Username.'),
//            'required'  => true,
        ));

        $fieldset->addField('conf/submit_text/email', 'text', array(
            'name'      => 'conf[submit_text][email]',
            'label'     => $this->__('Email'),
            'class'     => 'email',
            'maxlength' => '40',
            'value'     => isset($formData['conf[submit_text][email]']) ? $formData['conf[submit_text][email]'] : null,
            'note'      => $this->__('Paypal Merchant Account Email.'),
//            'required'  => true,
        ));

        $fieldset->addField('conf/submit_text/price_free', 'radio', array(
            'name'      => 'conf[submit_text][price_free]',
            'label'     => $this->__('Price'),
            'maxlength' => '40',
            'after_element_html' => $this->__('Free'),
            'onclick'    => "$('conf/submit_text/price').setValue('')",
        ));

        $fieldset->addField('conf/submit_text/price', 'text', array(
            'name'      => 'conf[submit_text][price]',
            'label'     => $this->__(' '),
            'maxlength' => '40',
            'value'     => isset($formData['conf[submit_text][price]']) ? $formData['conf[submit_text][price]'] : null,
            'note'      => $this->__('You can set any price you want for your app, or you can give it away for free. Most apps range from $0.99 - $4.99'),
            'onchange'  => "$('conf/submit_text/price_free').checked = false",

        ));

        $selected = isset($formData['conf[submit_text][country]']) ? json_decode($formData['conf[submit_text][country]']) : null;
        $fieldset->addField('conf/submit_text/country', 'multiselect', array(
            'name'      => 'conf[submit_text][country][]',
            'label'     => $this->__('Country'),
            'values'    => Mage::helper('xmlconnect')->getCountryOptionsArray(),
            'value'     => $selected,
            'note'      => $this->__('Make this app available in the following territories'),
        ));

        $fieldset->addField('conf/submit_text/country_additional', 'text', array(
            'name'      => 'conf[submit_text][country_additional]',
            'label'     => $this->__('Additional Countries'),
            'maxlength' => '200',
            'value'     => isset($formData['conf[submit_text][country_additional]']) ? $formData['conf[submit_text][country_additional]'] : null,
            'note'      => $this->__('You can set any additional countries added by Apple Store.'),

        ));

        $fieldset->addField('conf/submit_text/copyright', 'textarea', array(
            'name'      => 'conf[submit_text][copyright]',
            'label'     => $this->__('Copyright'),
            'maxlength' => '200',
            'value'     => isset($formData['conf[submit_text][copyright]']) ? $formData['conf[submit_text][copyright]'] : null,
            'note'      => $this->__('This will appear in the info section of your App (example:  Copyright 2010 – Your Company, Inc.)'),
            'size'      => '30',
//            'required'  => true,
        ));

        $fieldset->addField('conf/submit_text/push_notification', 'checkbox', array(
            'name'      => 'conf[submit_text][push_notification]',
            'label'     => $this->__('Push Notification'),
            'checked'   => isset($formData['conf[submit_text][push_notification]']),
            'value'     => '1',
        ));

        $fieldset = $form->addFieldset('submit1', array('legend' => $this->__('Icons')));
        $this->addImage($fieldset, 'conf/submit/icon', 'Application Icon',
            $this->__('Apply will automatically resize this image for display in the App Store and on users’ devices.  A gloss (i.e. gradient) will also be applied, so you do not need to apply a gradient.  Image must be at least 512x512'));
        $this->addImage($fieldset, 'conf/submit/loader_image', 'Loader Splash Screen',
            $this->__('Users will see this image as the first screen while your application is loading.  It is a 320x460 image.'));

        $this->addImage($fieldset, 'conf/submit/logo', 'Logo');
        $this->addImage($fieldset, 'conf/submit/big_logo', 'Big Logo');

        $fieldset = $form->addFieldset('submit2', array('legend' => $this->__('Key')));
        $fieldset->addField('conf[submit_text][key]', 'text', array(
            'name'      => 'conf[submit_text][key]',
            'label'     => $this->__('Activation Key'),
            'value'     => isset($formData['conf[submit_text][key]']) ? $formData['conf[submit_text][key]'] : null,
            'disabled'  => $isResubmit,
        ));

        if (!$isResubmit) {
            $fieldset->addField('submit', 'submit', array(
                'name' => 'submit_form',
                'label'=>$this->__('Submit'),
                'value' => $this->__('Submit Application'),
            ));
        } else {
            $fieldset->addField('submit', 'submit', array(
                'name' => 'submit_form',
                'label'=> $this->__('Resubmit'),
                'value' => $this->__('Resubmit Application'),
                'onclick' =>  'resubmit(); return false;',
            ));
        }

        $fieldset = $form->addFieldset('payment_disabled_fieldset', array('legend' => $this->__('Payment Methods')));

        $fieldset->addField('conf/native/paypal/isActive', 'checkbox', array(
            'name'      => 'conf[native][paypal][isActive]',
            'label'     => 'Activate paypal for this store',
            'value'     => 1,
            'checked'   => isset($formData['conf[native][paypal][isActive]']),
            'disabled'  => 'disabled',
        ));

        $fieldset->addField('conf/native/defaultCheckout/isActive', 'checkbox', array(
            'name'      => 'conf[native][defaultCheckout][isActive]',
            'label'     => 'Use Default Checkout method',
            'value'     => 1,
            'checked'   => isset($formData['conf[native][defaultCheckout][isActive]']),
            'disabled'  => 'disabled',
        ));

        $form->setAction($this->getUrl('*/*/editPost', array('key' => $application->getId())));
        $form->setMethod('post');

        $form->setId('submit_form');
        $form->setEnctype('multipart/form-data');
        $form->setUseContainer(true);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Submission');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Submission');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Configure image element type
     *
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('xmlconnect/adminhtml_mobile_helper_image'),
        );
    }

    protected function _toHtml()
    {
        return parent::_toHtml()
            . $this->getChildHtml('payment_disabled')
            . (!Mage::registry('current_app')->getIsResubmitAction() ? '' :
                ($this->getChildHtml('mobile_edit_tab_submission_history')
                . $this->getChildHtml('resubmit')
                . $this->getChildHtml('images')));
    }
}
