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

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Page Data')));

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
                'label' => __('Page Title'),
                'title' => __('Page Title'),
                'class' => 'required-entry',
                'required' => true,
                'value' => $this->getPageObject()->getPageTitle()
            )
        );

    	$fieldset->addField('page_identifier', 'text',
            array(
                'name' => 'page_identifier',
                'label' => __('Identifier'),
                'title' => __('Identifier'),
                'class' => 'required-entry',
                'required' => true,
                'value' => $this->getPageObject()->getPageIdentifier()
            )
        );

    	$fieldset->addField('page_active', 'radios',
            array(
                    'label'     => __('Status'),
                    'title'     => __('Page Status'),
                    'value'     => ( !is_null($this->getPageObject()->getPageActive()) ) ? $this->getPageObject()->getPageActive() : 1,
                    'class'     => 'validate-one-required',
                    'name'      => 'page_active',
                    'required' => true,
                    'values'    => array(
                        array(
                            'value' => 1,
                            'label' => ' Enabled',
                            'title' => 'Enabled Title'
                        ),
                        array(
                            'value' => 0,
                            'label' => ' Disabled',
                            'title' => 'Disabled Title'
                        )
                    )
            )
        );

    	$fieldset->addField('page_content', 'editor',
            array(
                'name' => 'page_content',
                'label' => __('Content'),
                'title' => __('Content'),
                'class' => 'required-entry',
                'wysiwyg' => true,
                'required' => true,
                'theme' => 'advanced',
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
