<?php
/**
 * Form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Form extends Mage_Adminhtml_Block_Widget
{
    protected $_form;
    //protected $_elementBlock;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('widget/form.phtml');
        $this->setDestElementId('edit_form');
    }

    public function getForm()
    {
        return $this->_form;
    }

    public function getFormObject()
    {
        return $this->getForm();
    }

    public function getFormHtml()
    {
        if (is_object($this->getForm())) {
            return $this->getForm()->getHtml();
        }
        return '';
    }

    public function setForm(Varien_Data_Form $form)
    {
        $this->_form = $form;
        $this->_form->setBaseUrl(Mage::getBaseUrl());
        return $this;
    }

    protected function _prepareForm()
    {
        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareForm();
        return $this;
    }

    /*protected function _getElementBlock()
    {
        if (!$this->_elementBlock) {
            $this->_elementBlock = $this->getLayout()->createBlock('adminhtml/widget_form_element');
        }
        return $this->_elementBlock;
    }*/

    /*public function drawElement(Varien_Data_Form_Abstract $element)
    {
        return $this->_getElementBlock()->setForm($this->getForm())
            ->setElement($element)
            ->setFormBlock($this)
            ->toHtml();
    }*/

    public function _setFieldset($attributes, $fieldset)
    {
        foreach ($attributes as $attribute) {
            if (!$attribute->getIsVisible()) {
                continue;
            }
            if ($inputType = $attribute->getFrontend()->getInputType()) {
                $element = $fieldset->addField($attribute->getName(), $inputType,
                    array(
                        'name'  => $attribute->getName(),
                        'label' => $attribute->getFrontend()->getLabel(),
                        'class' => $attribute->getFrontend()->getClass(),
                        'required' => $attribute->getIsRequired(),
                    )
                );
                if ($inputType == 'select') {
                    $element->setValues($attribute->getFrontend()->getSelectOptions());
                }
            }
        }
    }
}
