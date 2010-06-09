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

        $fieldset = $form->addFieldset('submit1', array('legend' => $this->__('Resubmission Fields')));
        $this->addImage($fieldset, 'conf[submit][appIcon]', 'Application Icon');
        $this->addImage($fieldset, 'conf[submit][loaderImage]', 'Loader Splash Screen');

        $fieldset = $form->addFieldset('submit2', array('legend' => $this->__('Key')));
        $fieldset->addField('conf[submit][key]', 'text', array(
            'name'      => 'conf[submit][key]',
            'label'     => $this->__('Activation Key'),
        ));

        // FIXME: submit button (here!)

        $model = Mage::registry('current_app');
        $form->setValues($model->getFormData());
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
}
