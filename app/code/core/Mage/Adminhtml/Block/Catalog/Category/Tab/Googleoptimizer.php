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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Adminhtml_Block_Catalog_Category_Tab_Googleoptimizer extends Mage_Adminhtml_Block_Catalog_Form
{
    public function __construct()
    {
        parent::__construct();
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
        return $this->getCategory()->getGoogleOptimizerCodes();
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $form->setDataObject($this->getCategory());

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalog')->__('Google Optimizer')));

        $fieldset->addField('control_script', 'textarea',
            array(
                'name'  => 'control_script',
                'label' => Mage::helper('googleoptimizer')->__('Control Script'),
                'class' => 'textarea',
                'required' => false,
                'note' => '',
            )
        );

        $fieldset->addField('tracking_script', 'textarea',
            array(
                'name'  => 'tracking_script',
                'label' => Mage::helper('googleoptimizer')->__('Tracking Script'),
                'class' => 'textarea',
                'required' => false,
                'note' => '',
            )
        );

        $fieldset->addField('conversion_script', 'textarea',
            array(
                'name'  => 'conversion_script',
                'label' => Mage::helper('googleoptimizer')->__('Conversion Script'),
                'class' => 'textarea',
                'required' => false,
                'note' => '',
            )
        );

        if ($this->getGoogleOptimizer()->getData()) {
            $fieldset->addField('code_id', 'hidden', array('name' => 'code_id'));
        }

        $fieldset->addField('export_controls', 'text',
            array(
                'name'  => 'export_controls',
            )
        );

        $form->getElement('export_controls')->setRenderer(
            $this->getLayout()->createBlock('adminhtml/catalog_form_renderer_googleoptimizer_import')
        );

        $form->addValues($this->getGoogleOptimizer()->getData());
        $form->setFieldNameSuffix('googleoptimizer');
        $this->setForm($form);
    }

}