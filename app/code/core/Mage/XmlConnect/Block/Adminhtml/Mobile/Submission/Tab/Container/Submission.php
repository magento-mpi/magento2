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

class Mage_XmlConnect_Block_Adminhtml_Mobile_Submission_Tab_Container_Submission
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
        if ($this->getApplication()->getIsResubmitAction()) {
            $block = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('xmlconnect/submission/app_icons_preview.phtml')
                ->setImages($this->getApplication()->getImages());
            $this->setChild('images', $block);
        }
        parent::_prepareLayout();
    }

    /**
     * Add image uploader to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     */
    protected function addImage($fieldset, $fieldName, $title, $note = '', $default = '')
    {
        $fieldset->addField($fieldName, 'image', array(
            'name'      => $fieldName,
            'label'     => $title,
            'note'      => !empty($note) ? $note : null,
        ));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $form->setAction($this->getUrl('*/mobile/submission'));
        $isResubmit = $this->getApplication()->getIsResubmitAction();
        $formData = $this->getApplication()->getFormData();

        $fieldset = $form->addFieldset('submit_general', array('legend' => Mage::helper('xmlconnect')->__('Submission Fields')));

        $fieldset->addField('submission_action', 'hidden', array(
            'name'      => 'submission_action',
            'value'     => '1',
        ));
        $fieldset->addField('conf/submit_text/title', 'text', array(
            'name'      => 'conf[submit_text][title]',
            'label'     => Mage::helper('xmlconnect')->__('Title'),
            'maxlength' => '200',
            'value'     => isset($formData['conf[submit_text][title]']) ? $formData['conf[submit_text][title]'] : null,
            'note'      => Mage::helper('xmlconnect')->__('This is the name that will appear beneath your app when users install it to their device.  .  We recommend choosing a name that is 10-12 characters in length, and that your customers will recognize.'),
            'required'  => true,
        ));

        $field = $fieldset->addField('conf/submit_text/description', 'textarea', array(
            'name'      => 'conf[submit_text][description]',
            'label'     => Mage::helper('xmlconnect')->__('Description'),
            'maxlength' => '500',
            'value'     => isset($formData['conf[submit_text][description]']) ? $formData['conf[submit_text][description]'] : null,
            'note'      => Mage::helper('xmlconnect')->__('This is the description that will appear in the iTunes marketplace. '),
            'required'  => true,
        ));
        $field->setRows(15);

        $fieldset->addField('conf/submit_text/contact_email', 'text', array(
            'name'      => 'conf[submit_text][email]',
            'label'     => Mage::helper('xmlconnect')->__('Contact Email'),
            'class'     => 'email',
            'maxlength' => '40',
            'value'     => isset($formData['conf[submit_text][email]']) ? $formData['conf[submit_text][email]'] : null,
            'note'      => Mage::helper('xmlconnect')->__('This will be a contact email address.'),
            'required'  => true,
        ));

        $fieldset->addField('conf/submit_text/price_free_label', 'label', array(
            'name'      => 'conf[submit_text][price_free_label]',
            'label'     => Mage::helper('xmlconnect')->__('Price'),
            'value'     => Mage::helper('xmlconnect')->__('Free'),
            'maxlength' => '40',
            'checked'   => 'checked',
            'note'      => Mage::helper('xmlconnect')->__('Only free applications are allowed in this version.'),
        ));

        $fieldset->addField('conf/submit_text/price_free', 'hidden', array(
            'name'      => 'conf[submit_text][price_free]',
            'value'     => '1',
        ));

        $selected = isset($formData['conf[submit_text][country]']) ? json_decode($formData['conf[submit_text][country]']) : null;
        $fieldset->addField('conf/submit_text/country', 'multiselect', array(
            'name'      => 'conf[submit_text][country][]',
            'label'     => Mage::helper('xmlconnect')->__('Country'),
            'values'    => Mage::helper('xmlconnect')->getCountryOptionsArray(),
            'value'     => $selected,
            'note'      => Mage::helper('xmlconnect')->__('Make this app available in the following territories'),
        ));

        $fieldset->addField('conf/submit_text/country_additional', 'text', array(
            'name'      => 'conf[submit_text][country_additional]',
            'label'     => Mage::helper('xmlconnect')->__('Additional Countries'),
            'maxlength' => '200',
            'value'     => isset($formData['conf[submit_text][country_additional]']) ? $formData['conf[submit_text][country_additional]'] : null,
            'note'      => Mage::helper('xmlconnect')->__('You can set any additional countries added by Apple Store.'),

        ));

        $fieldset->addField('conf/submit_text/copyright', 'text', array(
            'name'      => 'conf[submit_text][copyright]',
            'label'     => Mage::helper('xmlconnect')->__('Copyright'),
            'maxlength' => '200',
            'value'     => isset($formData['conf[submit_text][copyright]']) ? $formData['conf[submit_text][copyright]'] : null,
            'note'      => Mage::helper('xmlconnect')->__('This will appear in the info section of your App (example:  Copyright 2010 – Your Company, Inc.)'),
            'required'  => true,
        ));

        $fieldset->addField('conf/submit_text/keywords', 'text', array(
            'name'      => 'conf[submit_text][keywords]',
            'label'     => Mage::helper('xmlconnect')->__('Keywords'),
            'maxlength' => '100',
            'value'     => isset($formData['conf[submit_text][keywords]']) ? $formData['conf[submit_text][keywords]'] : null,
            'note'      => Mage::helper('xmlconnect')->__('One or more keywords that describe your app. Keywords are matched to users\' searches in the App Store and help return accurate search results. Separate multiple keywords with commas. 100 chars is maximum.'),
        ));

        $fieldset = $form->addFieldset('submit_icons', array('legend' => Mage::helper('xmlconnect')->__('Icons')));
        $this->addImage($fieldset, 'conf/submit/icon', 'Application Icon',
            Mage::helper('xmlconnect')->__('Apply will automatically resize this image for display in the App Store and on users’ devices.  A gloss (i.e. gradient) will also be applied, so you do not need to apply a gradient.  Image must be at least 512x512'));
        $this->addImage($fieldset, 'conf/submit/loader_image', 'Loader Splash Screen',
            Mage::helper('xmlconnect')->__('Users will see this image as the first screen while your application is loading.  It is a 320x460 image.'));

        $this->addImage($fieldset, 'conf/submit/logo', Mage::helper('xmlconnect')->__('Custom application icon'),
            Mage::helper('xmlconnect')->__('This icon will be  used for users’ devices in case if different than AppSore icon needed. '));
        $this->addImage($fieldset, 'conf/submit/big_logo', Mage::helper('xmlconnect')->__('Copyright page logo'),
            Mage::helper('xmlconnect')->__('Store logo that will be displayed on copyright page of application '));

        $fieldset = $form->addFieldset('submit_keys', array('legend' => Mage::helper('xmlconnect')->__('Key')));
        $field = $fieldset->addField('conf[submit_text][key]', 'text', array(
            'name'      => 'conf[submit_text][key]',
            'label'     => Mage::helper('xmlconnect')->__('Activation Key'),
            'value'     => isset($formData['conf[submit_text][key]']) ? $formData['conf[submit_text][key]'] : null,
            'disabled'  => $isResubmit,
            'after_element_html' => '<a href="' .
                Mage::getStoreConfig('xmlconnect/mobile_application/get_activation_key_url') . '" target="_blank">'
                . Mage::helper('xmlconnect')->__('Get Activation Key'). '</a>',
        ));
        if (!$isResubmit) {
            $field->setRequired(true);
        }

        $url = Mage::getStoreConfig('xmlconnect/mobile_application/get_activation_key_url');
        $afterElementHtml = Mage::helper('xmlconnect')->__('In order to resubmit your app, you need to first purchase a <a href="%s" target="_blank">%s</a> from Magentocommerce', $url, Mage::helper('xmlconnect')->__('resubmission key'));

        if ($isResubmit)
        $fieldset->addField('conf[submit_text][resubmission_activation_key]', 'text', array(
            'name'     => 'conf[submit_text][resubmission_activation_key]',
            'label'    => Mage::helper('xmlconnect')->__('Resubmission Key'),
            'value'    => isset($formData['conf[submit_text][resubmission_activation_key]']) ? $formData['conf[submit_text][resubmission_activation_key]'] : null,
            'required' => true,
            'after_element_html' => $afterElementHtml,
        ));
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('xmlconnect')->__('Submission');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('xmlconnect')->__('Submission');
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
        return parent::_toHtml() . (!$this->getApplication()->getIsResubmitAction() ? '' : $this->getChildHtml('images'));
    }
}
