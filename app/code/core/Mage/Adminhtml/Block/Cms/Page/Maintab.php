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
    }

    protected function _initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_main');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('main data')));

        if( intval($this->getPageObject()->getPageId()) > 0 ) {
        	$fieldset->addField('page_id', 'hidden',
                array(
                    'name' => 'page_id',
                    'value' => $this->getPageObject()->getPageId()
                )
            );
        }

    	$fieldset->addField('page_title', 'text',
            array(
                'name' => 'page_title',
                'label' => __('page title'),
                'title' => __('page title'),
                'class' => 'required-entry',
                'value' => $this->getPageObject()->getPageTitle()
            )
        );

    	$fieldset->addField('page_identifier', 'text',
            array(
                'name' => 'page_identifier',
                'label' => __('page identifier'),
                'title' => __('page identifier title'),
                'class' => 'required-entry',
                'value' => $this->getPageObject()->getPageIdentifier()
            )
        );

    	$fieldset->addField('page_active', 'radios',
            array(
                    'label'     => __('page enabled'),
                    'title'     => __('page enabled title'),
                    'value'     => ( !is_null($this->getPageObject()->getPageActive()) ) ? $this->getPageObject()->getPageActive() : 1,
                    'class'     => 'required-entry',
                    'name'      => 'page_active',
                    'values'    => array(
                        array(
                            'value' => 1,
                            'label' => 'enabled',
                            'title' => 'enabled title'
                        ),
                        array(
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
                'wysiwyg' => true,
                'value' => $this->getPageObject()->getPageContent()
            )
        );

        $this->setForm($form);
    }

    protected function _beforeToHtml()
    {
        $this->_initForm();
        return parent::_beforeToHtml();
    }
}
