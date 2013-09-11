<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Rating_Edit_Tab_Options extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $form = $this->_createForm();

        $fieldset = $form->addFieldset('options_form', array('legend'=>__('Assigned Options')));

        if ($this->_coreRegistry->registry('rating_data')) {
            $collection = Mage::getModel('Magento_Rating_Model_Rating_Option')
                ->getResourceCollection()
                ->addRatingFilter($this->_coreRegistry->registry('rating_data')->getId())
                ->load();

            $i = 1;
            foreach ($collection->getItems() as $item) {
                $fieldset->addField('option_code_' . $item->getId() , 'text', array(
                    'label'     => __('Option Label'),
                    'required'  => true,
                    'name'      => 'option_title[' . $item->getId() . ']',
                    'value'     => ( $item->getCode() ) ? $item->getCode() : $i,
                ));
                $i ++;
            }
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $fieldset->addField('option_code_' . $i, 'text', array(
                    'label'     => __('Option Title'),
                    'required'  => true,
                    'name'      => 'option_title[add_' . $i . ']',
                    'value'     => $i,
                ));
            }
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

}
