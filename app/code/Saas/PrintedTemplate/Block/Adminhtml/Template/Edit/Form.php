<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml system template edit form
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Adminhtml_Template_Edit_Form extends Mage_Backend_Block_Widget_Form
{
    /**
     * Prepare layout.
     * Add files to use dialog windows
     *
     * @return Saas_PrintedTemplate_Block_Adminhtml_Template_Edit
     */
    protected function _prepareLayout()
    {
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->addJs('prototype/window.js')
                ->addCss('prototype/windows/themes/default.css')
                ->addCss('Mage_Core::prototype/magento.css')
                ->setCanLoadTinyMce(true);
        }
        return parent::_prepareLayout();
    }

    /**
     * Add fields to form and create template info form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('printedtemplate_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => __('Template Information'),
            'class'     => 'fieldset-wide'
        ));
        $fieldset->addType('composite', 'Saas_PrintedTemplate_Block_Widget_Form_Element_Composite');

        $templateId = $this->getPrintedTemplate()->getId();

        if ($templateId && $usedForValue = $this->_getUsedCurrentlyForValue()) {
            $fieldset->addField('used_currently_for', 'note', array(
                'label' => __('Used Currently For'),
                'container_id' => 'used_currently_for',
                'text' => $usedForValue
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => __('Template Name'),
            'required'  => true
        ));

        $fieldset->addField('page_size', 'select', array(
            'name'   => 'page_size',
            'label'  => __('Page Size'),
            'title'  => __('Page Size'),
            'required'  => true,
            'values' => Mage::getModel('Saas_PrintedTemplate_Model_Source_PageSize')->toOptionArray()
        ));

        $fieldset->addField('page_orientation', 'select', array(
            'name'   => 'page_orientation',
            'label'  => __('Page Orientation'),
            'title'  => __('Page Orientation'),
            'required'  => true,
            'values' => Mage::getModel('Saas_PrintedTemplate_Model_Source_PageOrientation')->toOptionArray()
        ));

        $configData = array();
        if ($this->getPrintedTemplate()->getEntityType() == Saas_PrintedTemplate_Model_Template::ENTITY_TYPE_SHIPMENT) {
            $configData['skip_widgets'] = array('Saas_PrintedTemplate_Block_Widget_TaxGrid');
        }
        $wysiwygConfig = Mage::getSingleton('Saas_PrintedTemplate_Model_Wysiwyg_Config')->getConfig($configData);

        $dynamicHeightsOn = $this->_isDynamicHeightsEnabled();
        if ($dynamicHeightsOn) {
            $fieldset->addField('header_auto_height', 'checkbox', array(
                'name' => 'header_auto_height',
                'label' => __('Auto Header Height'),
                'title' => __('Calculate header height automatiacally'),
                'checked' => true,
                'onchange' => 'templateControl.updateHeightFields(this, \'header\')'
            ));
        }

        $headerSize = $fieldset->addField('header_size_composite', 'composite', array(
            'label' => __('Header Height')
        ));

        $headerSize->addField('header_height', 'text', array(
            'name' => 'header_height',
            'title' => __('Header Height'),
            'maxlength' => 6,
            'class' => 'validate-number height-field',
            'value' => 0,
        ));
        $headerSize->addField('header_height_measurement', 'select', array(
            'name' => 'header_height_measurement',
            'title' => __('Header Height Measure'),
            'values' => Mage::getSingleton("Saas_PrintedTemplate_Model_Source_Measurement")->toOptionArray(),
            'class' => 'height-measure-field'
        ));

        if ($dynamicHeightsOn) {
            $fieldset->addField('footer_auto_height', 'checkbox', array(
                'name' => 'footer_auto_height',
                'label' => __('Auto Footer Height'),
                'title' => __('Calculate footer height automatiacally'),
                'checked' => true,
                'onchange' => 'templateControl.updateHeightFields(this, \'footer\')'
            ));
        }

        $footerSize = $fieldset->addField('footer_size_composite', 'composite',
            array('label' => __('Footer Height'))
        );
        $footerSize->addField('footer_height', 'text', array(
            'name' => 'footer_height',
            'title' => __('Footer Height'),
            'maxlength' => 6,
            'class' => 'validate-number height-field',
            'value' => 0,
        ));
        $footerSize->addField('footer_height_measurement', 'select', array(
            'name' => 'footer_height_measurement',
            'title' => __('Footer Height Measure'),
            'class' => 'height-measure-field',
            'values' => Mage::getSingleton("Saas_PrintedTemplate_Model_Source_Measurement")->toOptionArray()
        ));


        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => __('Template Content'),
            'title'     => __('Template Content'),
            'required'  => true,
            'style'     => 'height:24em;',
            'state'     => 'html',
            'config'    => $wysiwygConfig
        ));

        $fieldset->addField('entity_type', 'hidden', array(
            'name'      => 'entity_type',
            'value'     => $this->getPrintedTemplate()->getEntityType(),
        ));

        if ($templateId) {
            $formData = $this->getPrintedTemplate()->getData();
            $formData['header_height'] = (float)$formData['header_height'];
            $formData['footer_height'] = (float)$formData['footer_height'];
            $formData['content'] = Mage::getSingleton('Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser')
                ->exportContent($this->getPrintedTemplate());
            $form->addValues($formData);
            $dynamicHeightsOn = $this->_isDynamicHeightsEnabled();
            if ($dynamicHeightsOn) {
                $form->getElement('header_auto_height')->setIsChecked($formData['header_auto_height']);
                $form->getElement('footer_auto_height')->setIsChecked($formData['footer_auto_height']);
            }
        }

        $values = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getData('printed_template_form_data', true);
        if ($values) {
            $form->setValues($values);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Return current printed template model
     *
     * @return Saas_PrintedTemplate_Model_Template
     */
    public function getPrintedTemplate()
    {
        return Mage::registry('current_printed_template');
    }

    /**
     * Retrieve html for used_currently_for element value
     *
     * @return string
     */
    protected function _getUsedCurrentlyForValue()
    {
        $pathDelimiter = '<span class="path-delimiter">&nbsp;-&gt;&nbsp;</span>';
        $lineDelimiter = '<br />';
        $pathsUsedCurrently = Mage::helper('Saas_PrintedTemplate_Helper_Data')
            ->getSystemConfigPathsParts($this->getPrintedTemplate()->getSystemConfigPathsWhereUsedCurrently());
        $result = array();
        foreach ($pathsUsedCurrently as $pathArray) {
            $path = array();
            foreach ($pathArray as $pathNode) {
                $path[] = (isset($pathNode['url']) && $pathNode['url']
                    ? "<a href=\"{$pathNode['url']}\">{$pathNode['title']}</a>" : $pathNode['title'])
                        . (isset($pathNode['scope']) && $pathNode['scope']
                            ? "&nbsp;&nbsp;<span class=\"path-scope-label\">({$pathNode['scope']})" : '');
            }
            $result[] = implode($pathDelimiter, $path);
        }

        return implode($lineDelimiter, $result);
    }

    /**
     * Check if PDF adapter supports dynamic header/footer size calculation
     *
     * @return bool
     */
    protected function _isDynamicHeightsEnabled()
    {
        try {
            return Mage::helper('Saas_PrintedTemplate_Helper_Locator')
                ->getPdfRenderer()
                ->canCalculateHeightsDynamically();
        } catch (Mage_Core_Exception $e) {
            return false;
        }
    }

}
