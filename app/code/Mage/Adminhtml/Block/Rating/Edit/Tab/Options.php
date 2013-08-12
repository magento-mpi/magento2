<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Rating_Edit_Tab_Options extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Magento_Data_Form();

        $fieldset = $form->addFieldset('options_form', array('legend'=>__('Assigned Options')));

        if( Mage::registry('rating_data') ) {
            $collection = Mage::getModel('Mage_Rating_Model_Rating_Option')
                ->getResourceCollection()
                ->addRatingFilter(Mage::registry('rating_data')->getId())
                ->load();

            $i = 1;
            foreach( $collection->getItems() as $item ) {
                $fieldset->addField('option_code_' . $item->getId() , 'text', array(
                                        'label'     => __('Option Label'),
                                        'required'  => true,
                                        'name'      => 'option_title[' . $item->getId() . ']',
                                        'value'     => ( $item->getCode() ) ? $item->getCode() : $i,
                                    )
                );
                $i ++;
            }
        } else {
            for( $i=1;$i<=5;$i++ ) {
                $fieldset->addField('option_code_' . $i, 'text', array(
                                        'label'     => __('Option Title'),
                                        'required'  => true,
                                        'name'      => 'option_title[add_' . $i . ']',
                                        'value'     => $i,
                                    )
                );
            }
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

}
