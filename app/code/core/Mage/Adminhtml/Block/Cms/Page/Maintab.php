<?php
/**
 * Customer account form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Cms_Page_Maintab extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->_initForm();
    }

    protected function _initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_main');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('main data')));

    	$fieldset->addField('page_title', 'text',
            array(
                'name' => 'page_title',
                'label' => __('page title'),
                'title' => __('page title'),
                'class' => 'required-entry'
            )
        );

    	$fieldset->addField('page_identifier', 'text',
            array(
                'name' => 'page_identifier',
                'label' => __('page identifier'),
                'title' => __('page identifier title'),
                'class' => 'required-entry'
            )
        );

    	$fieldset->addField('page_active', 'radio',
            array(
                    'label' => __('page enabled'),
                    'title' => __('page enabled title'),
                    'checked' => 'page_enabled',
                    'class' => 'required-entry',

                    'radios' => array(
                        'page_enabled' => array(
                            'name' => 'page_active',
                            'value' => 1,
                            'label' => 'enabled',
                            'title' => 'enabled title'
                        ),

                        'page_disabled' => array(
                            'name' => 'page_active',
                            'value' => 0,
                            'label' => 'disabled',
                            'title' => 'disabled title'
                        )
                    )
            )
        );

    	$fieldset->addField('page_content', 'editor',
            array(
                'name' => 'page_content',
                'label' => __('page content'),
                'title' => __('page content title'),
                'class' => 'required-entry',
                'wysiwyg' => true
            )
        );

        $this->setForm($form);
    }
}
