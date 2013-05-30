<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Optimizer Category Tab
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Category_Edit_Tab_Googleoptimizer
    extends Mage_Adminhtml_Block_Catalog_Form
{
    /**
     * Conversion page URL to form
     *
     * @var Mage_GoogleOptimizer_Block_Adminhtml_ConversionPageUrl_FormUpdater
     */
    protected $_conversionPage;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_GoogleOptimizer_Block_Adminhtml_ConversionPageUrl_FormUpdater $conversionPage
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_GoogleOptimizer_Block_Adminhtml_ConversionPageUrl_FormUpdater $conversionPage,
        array $data = array()
    ) {
        $this->_conversionPage = $conversionPage;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = Mage::registry('current_category');
        }
        return $this->_category;
    }

    public function getGoogleOptimizer()
    {
        return $this->getCategory()->getGoogleOptimizerScripts();
    }

    public function _prepareLayout()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Google Optimizer Scripts'))
        );

        if ($this->getCategory()->getStoreId() == '0') {
            Mage::helper('Mage_GoogleOptimizer_Helper_Data')->setStoreId(Mage::app()->getDefaultStoreView());
        } else {
            Mage::helper('Mage_GoogleOptimizer_Helper_Data')->setStoreId($this->getCategory()->getStoreId());
        }

        $disabledScriptsFields = false;
        $values = array();
        if ($this->getGoogleOptimizer() && $this->getGoogleOptimizer()->getData()) {
            $disabledScriptsFields = true;
            $values = $this->getGoogleOptimizer()->getData();
            $checkedUseDefault = true;
            if ($this->getGoogleOptimizer()->getStoreId() == $this->getCategory()->getStoreId()) {
                $checkedUseDefault = false;
                $disabledScriptsFields = false;
                $fieldset->addField('code_id', 'hidden', array('name' => 'code_id'));
            }
            // show 'use default' checkbox if store different for default and product has scripts for default store
            if ($this->getCategory()->getStoreId() != '0') {
                $fieldset->addField('store_flag', 'checkbox',
                    array(
                        'name'  => 'store_flag',
                        'value' => '1',
                        'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Use Default'),
                        'class' => 'checkbox',
                        'required' => false,
                        'onchange' => 'googleOptimizerScopeAction()',
                    )
                )->setIsChecked($checkedUseDefault);
            }
        }

        $fieldset->addField('conversion_page', 'select',
            array(
                'name'  => 'conversion_page',
                'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Conversion Page'),
                'values'=>
                    Mage::getModel('Mage_GoogleOptimizer_Model_Adminhtml_System_Config_Source_Googleoptimizer_Conversionpages')
                        ->toOptionArray(),
                'class' => 'select googleoptimizer validate-googleoptimizer',
                'required' => false,
                'onchange' => 'googleOptimizerConversionPageAction(this)'
            )
        );

        $this->_conversionPage->update($this->getCategory()->getStoreId(), $fieldset);

        $fieldset->addField('export_controls', 'text', array('name' => 'export_controls'));

        $fieldset->addField('control_script', 'textarea',
            array(
                'name'  => 'control_script',
                'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Control Script'),
                'class' => 'textarea googleoptimizer validate-googleoptimizer',
                'required' => false,
            )
        );

        $fieldset->addField('tracking_script', 'textarea',
            array(
                'name'  => 'tracking_script',
                'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Tracking Script'),
                'class' => 'textarea googleoptimizer validate-googleoptimizer',
                'required' => false,
            )
        );

        $fieldset->addField('conversion_script', 'textarea',
            array(
                'name'  => 'conversion_script',
                'label' => Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('Conversion Script'),
                'class' => 'textarea googleoptimizer validate-googleoptimizer',
                'required' => false,
            )
        );

        if (Mage::helper('Mage_GoogleOptimizer_Helper_Data')->getConversionPagesUrl()
            && $this->getGoogleOptimizer()
            && $this->getGoogleOptimizer()->getConversionPage())
        {
            $form->getElement('conversion_page_url')
                ->setValue(Mage::helper('Mage_GoogleOptimizer_Helper_Data')
                    ->getConversionPagesUrl()->getData($this->getGoogleOptimizer()->getConversionPage())
                );
        }

        if ($disabledScriptsFields) {
            foreach ($fieldset->getElements() as $element) {
                if ($element->getType() == 'textarea' || $element->getType() == 'select') {
                    $element->setDisabled($disabledScriptsFields);
                }
            }
        }

        $fakeEntityAttribute = Mage::getModel('Mage_Catalog_Model_Resource_Eav_Attribute');

        $readonly = $this->getCategory()->getOptimizationReadonly();
        foreach ($fieldset->getElements() as $element) {
            $element->setDisabled($readonly);
            if ($element->getId() != 'store_flag') {
                $element->setEntityAttribute($fakeEntityAttribute);
            }
        }

        $form->getElement('export_controls')->setRenderer(
            $this->getLayout()->createBlock('Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Form_Renderer_Import')
        );

        $form->addValues($values);
        $form->setFieldNameSuffix('googleoptimizer');
        $this->setForm($form);

        return parent::_prepareLayout();
    }

}
