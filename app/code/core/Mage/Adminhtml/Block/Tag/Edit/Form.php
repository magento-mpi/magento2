<?php
/**
 * Tag edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Tag_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setDestElementId('tag_edit_form');
    }

    protected function _prepareForm()
    {
        $tag = Mage::registry('tag');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Tag Information')));

        $fieldset->addField('tagname', 'text', array(
            'name'  => 'tagname',
            'label' => __('Tag Name'),
            'title' => __('Tag Name Title'),
            'class' => 'required-entry',
            'value' => $tag->getTagname(),
        ));
        $fieldset->addField('status', 'checkbox', array(
            'name'  => 'status',
            'label' => __('Approved'),
            'title' => __('Approved Title'),
            'value' => 1,
        ))->setIsChecked($tag->getStatus());
        $fieldset->addField('store_id', 'hidden', array(
            'name'  => 'store_id',
            'value' => $tag->getStoreId(),
        ));

        $this->setForm($form);
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    public function getTagId()
    {
        return Mage::registry('tag')->getId();
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }

    public function getHeader()
    {
        if (Mage::registry('tag')->getId()) {
            return Mage::registry('tag')->getName();
        }
        else {
            return __('New Tag');
        }
    }
}
