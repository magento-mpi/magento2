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
 * @package    Mage_GoogleOptimizer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Optimizer Tab
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Googleoptimizer_Block_Adminhtml_Catalog_Product_Edit_Tab_Googleoptimizer
    extends Mage_Adminhtml_Block_Catalog_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_googleoptimizerModel = null;

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

//        $form->setDataObject($this->getGoogleOptimizer());
        $form->setDataObject($this->getProduct());

        $fieldset = $form->addFieldset('googleoptimizer_fields',
            array('legend'=>Mage::helper('googleoptimizer')->__('Google Optimizer Codes'))
        );

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

        $fakeEntityAttribute = Mage::getModel('catalog/resource_eav_attribute')
            ->setData('is_global', true);
        /** @var $fakeEntityAttribute Mage_Catalog_Model_Resource_Eav_Attribute */

        /**
         * setting fake entity attribute to elements. scope logic need this object
         */
        foreach ($fieldset->getElements() as $element) {
            $element->setEntityAttribute($fakeEntityAttribute);
        }

        if ($this->getGoogleOptimizer()->getData()) {
            $fieldset->addField('code_id', 'hidden', array('name' => 'code_id'));
        }

        $form->addValues($this->getGoogleOptimizer()->getData());
        $form->setFieldNameSuffix('googleoptimizer');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    public function getGoogleOptimizer()
    {
        return $this->getProduct()->getGoogleOptimizerCodes();
    }

    public function getTabLabel()
    {
        return Mage::helper('googleoptimizer')->__('Google Optimizer Codes');
    }

    public function getTabTitle()
    {
        return Mage::helper('googleoptimizer')->__('Google Optimizer Codes');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

}
