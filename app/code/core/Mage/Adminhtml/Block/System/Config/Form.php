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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * System config form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Form extends Mage_Adminhtml_Block_Widget_Form
{
    const SCOPE_DEFAULT = 'default';
    const SCOPE_WEBSITE = 'website';
    const SCOPE_STORE   = 'store';

    public function __construct()
    {
        parent::__construct();
    }

    public function initForm()
    {
        /**
         * @see  Varien_Object::__call()
         */
        // get section fields from config xml

        $sectionCode = $this->getRequest()->getParam('section');
        $websiteCode = $this->getRequest()->getParam('website');
        $storeCode = $this->getRequest()->getParam('store');

        $isDefault = !$websiteCode && !$storeCode;

        // get config section data from database
        $configData = Mage::getResourceModel('adminhtml/config')
            ->loadSectionData($sectionCode, $websiteCode, $storeCode);


        //            $configFields = new Mage_Adminhtml_Model_Config();
        //            $groups = $configFields->getGroups($sectionCode, $websiteCode, $storeCode);

        $configFields = Mage::getSingleton('adminhtml/config');
        $groups=$configFields->getSection($sectionCode, $websiteCode, $storeCode);

        $form = new Varien_Data_Form();

        $defaultFieldsetRenderer = Mage::getHelper('adminhtml/system_config_form_fieldset');
        $defaultFieldRenderer = Mage::getHelper('adminhtml/system_config_form_field');
        $fieldset = array();

        foreach ($groups as $group) {
            if (!$this->_canShowField($group)) {
                continue;
            }
            foreach ($group->sections as $sections){

                foreach ($sections as $section){
                    if (!$this->_canShowField($section)) {
                        continue;
                    }

                    if ($section->frontend_model) {
                        $fieldsetRenderer = Mage::getHelper((string)$section->frontend_model);
                    } else {
                        $fieldsetRenderer = $defaultFieldsetRenderer;
                    }

                    $fieldsetRenderer->setForm($this);
                    $fieldsetRenderer->setConfigData($configData);

                    $fieldset[$section->getName()] = $form->addFieldset($section->getName(), array(
                    'legend'=>__((string)$section->label)
                    ))->setRenderer($fieldsetRenderer);
                    $this->_addElementTypes($fieldset[$section->getName()]);
                    foreach ($section->fields as $elements){
                        foreach ($elements as $e){
                            if (!$this->_canShowField($e)) {
                                continue;
                            }
                            $path=$group->getName().'/'.$section->getName().'/'.$e->getName();
                            $id=$group->getName().'_'.$section->getName().'_'.$e->getName();
                            if (isset($configData[$path])) {
                                $data = $configData[$path];
                            } else {
                                $data = array('value'=>'', 'default_value'=>'', 'old_value'=>'', 'inherit'=>'');
                            }
                            if ($e->frontend_model) {
                                $fieldRenderer = Mage::getHelper((string)$e->frontend_model);
                            } else {
                                $fieldRenderer = $defaultFieldRenderer;
                            }

                            $fieldRenderer->setForm($this);
                            $fieldRenderer->setConfigData($configData);

                            $fieldType = (string)$e->frontend_type;

                            $field = $fieldset[$section->getName()]->addField(
                            $id, $fieldType ? $fieldType : 'text',
                                array(
                                    'name'          => 'groups['.$section->getName().'][fields]['.$e->getName().'][value]',
                                    'label'         => __((string)$e->label),
                                    'value'         => isset($data['value']) ? $data['value'] : '',
                                    'default_value' => isset($data['default_value']) ? $data['default_value'] : '',
                                    'old_value'     => isset($data['old_value']) ? $data['old_value'] : '',
                                    'inherit'       => isset($data['inherit']) ? $data['inherit'] : '',
                                    'class'         => $e->frontend_model,
                                    'can_use_default_value' => $this->canUseDefaultValue((int)$e->show_in_default),
                                    'can_use_website_value' => $this->canUseWebsiteValue((int)$e->show_in_website),
                                ))->setRenderer($fieldRenderer);
                            if ($srcModel = (string)$e->source_model) {
                                $field->setValues(Mage::getSingleton($srcModel)->toOptionArray($fieldType == 'multiselect'));
                            }
                        }
                    }
                }
            }
        }

        $this->setForm($form);
        return $this;
    }

    public function canUseDefaultValue($field)
    {
        if ($this->getScope() == self::SCOPE_STORE && $field) {
            return true;
        }
        if ($this->getScope() == self::SCOPE_WEBSITE && $field) {
            return true;
        }
        return false;
    }

    public function canUseWebsiteValue($field)
    {
        if ($this->getScope() == self::SCOPE_STORE && $field) {
            return true;
        }
        return false;
    }

    /**
     * Checking field visibility
     *
     * @param   Varien_Object $field
     * @return  bool
     */
    protected function _canShowField($field)
    {
        switch ($this->getScope()) {
            case self::SCOPE_DEFAULT:
                return $field->show_in_default;
                break;
            case self::SCOPE_WEBSITE:
                return $field->show_in_website;
                break;
            case self::SCOPE_STORE:
                return $field->show_in_store;
                break;
        }
        return true;
    }

    /**
     * Retrieve current scope
     *
     * @return string
     */
    public function getScope()
    {
        $scope = $this->getData('scope');
        if (is_null($scope)) {
            $sectionCode = $this->getRequest()->getParam('section');
            $websiteCode = $this->getRequest()->getParam('website');
            $storeCode = $this->getRequest()->getParam('store');

            if (!$websiteCode && !$storeCode) {
                $scope = self::SCOPE_DEFAULT;
            }
            elseif ($storeCode) {
                $scope = self::SCOPE_STORE;
            }
            elseif ($websiteCode) {
                $scope = self::SCOPE_WEBSITE;
            }
            else {
                $scope = false;
            }
            $this->setData('scope', $scope);
        }

        return $scope;
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'export' => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_export'),
            'import' => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_import'),
        );
    }
}
