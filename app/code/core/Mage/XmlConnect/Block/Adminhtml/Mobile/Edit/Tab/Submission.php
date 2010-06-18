

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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Submission extends Mage_Adminhtml_Block_Widget_Form
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

        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('xmlconnect/resubmit.phtml')
            ->setActionUrl($this->getUrl('xmlconnect/mobile/submit', array('application_id', $application->getId())))
            ->setActionvationKey($application->getActivationKey())
            ->setResubmissionName('conf[submit_text][resubmission_activation_key]');
        $this->setChild('resubmit', $block);
        parent::_prepareLayout();
    }


    /**
     * Add image uploader to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     */
    protected function addImage($fieldset, $fieldName, $title)
    {
        $fieldset->addField($fieldName, 'image', array(
            'name'      => $fieldName,
            'label'     => $this->__($title),
        ));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('submit0', array('legend' => $this->__('Submission Fields')));
        $fieldset->addField('conf[submit_text][title]', 'text', array(
            'name'      => 'conf[submit_text][title]',
            'label'     => $this->__('Title'),
            'maxlength' => '200',
//            'required'  => true,

        ));

        $fieldset->addField('conf[submit_text][description]', 'textarea', array(
            'name'      => 'conf[submit_text][description]',
            'label'     => $this->__('Description'),
            'maxlength' => '500',
//            'required'  => true,
        ));

        $fieldset->addField('conf[submit_text][username]', 'text', array(
            'name'      => 'conf[submit_text][username]',
            'label'     => $this->__('Username'),
            'maxlength' => '40',
//            'required'  => true,
        ));

        $fieldset->addField('conf[submit_text][email]', 'text', array(
            'name'      => 'conf[submit_text][email]',
            'label'     => $this->__('Email'),
            'class'     => 'email',
            'maxlength' => '40',
//            'required'  => true,
        ));

        $fieldset->addField('conf[submit_text][paypal_is_active]', 'checkbox', array(
            'name'      => 'conf[submit_text][paypa_is_active]',
            'label'     => $this->__('Activate paypal for this store'),
        ));

        $fieldset->addField('conf[submit_text][price]', 'text', array(
            'name'      => 'conf[submit_text][price]',
            'label'     => $this->__('Price'),
            'maxlength' => '40',
        ));

        $fieldset->addField('conf[submit_text][copyright]', 'text', array(
            'name'      => 'conf[submit_text][copyright]',
            'label'     => $this->__('Copyright'),
            'maxlength' => '200',
//            'required'  => true,
        ));

        $fieldset->addField('conf[submit_text][push_notification]', 'checkbox', array(
            'name'      => 'conf[submit_text][push_notification]',
            'label'     => $this->__('Push Notification'),
//            'options'   => array('-1' => 'Please Select', '1' => $this->__('Yes'), '0' => $this->__('No')),
//            'value'     => '-1',

        ));

        $fieldset = $form->addFieldset('submit1', array('legend' => $this->__('Submission Fields')));
        $this->addImage($fieldset, 'conf/submit/appIcon', 'Application Icon');
        $this->addImage($fieldset, 'conf/submit/loaderImage', 'Loader Splash Screen');

        $this->addImage($fieldset, 'conf/submit/logo', 'Logo');
        $this->addImage($fieldset, 'conf/submit/big_logo', 'Big Logo');

        $fieldset = $form->addFieldset('submit2', array('legend' => $this->__('Key')));
        $fieldset->addField('conf[submit_text][key]', 'text', array(
            'name'      => 'conf[submit_text][key]',
            'label'     => $this->__('Activation Key'),
        ));

        $model = Mage::registry('current_app');

        $form->setAction($this->getUrl('*/*/editPost', array('key' => $model->getId())));
        $form->setMethod('post');

        $form->setValues($model->getFormData());
        $form->setId('submit_form');
        $form->setEnctype('multipart/form-data');
        $form->setUseContainer(true);

        // put it after $form->setValues()
        if (!$model->getIsResubmitAction()) {
            $fieldset->addField('submit', 'submit', array(
                'name' => 'submit_form',
                'label'=>$this->__('Submit'),
                'value' => $this->__('Submit Application')
            ));
        } else {
            $fieldset->addField('submit', 'submit', array(
                'name' => 'submit_form',
                'label'=>$this->__('Resubmit'),
                'value' => $this->__('Resubmit Application'),
                'onclick' =>  'resubmit(); return false;'
            ));
        }

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
            . $this->getChildHtml('mobile_edit_tab_submission_history')
            . $this->getChildHtml('resubmit');
    }
}
