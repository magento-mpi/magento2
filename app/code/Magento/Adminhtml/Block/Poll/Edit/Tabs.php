<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin poll left menu
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Poll\Edit;

class Tabs extends \Magento\Adminhtml\Block\Widget\Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('poll_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Poll Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => __('Poll Information'),
            'title'     => __('Poll Information'),
            'content'   => $this->getLayout()->createBlock('Magento\Adminhtml\Block\Poll\Edit\Tab\Form')->toHtml(),
        ))
        ;

        $this->addTab('answers_section', array(
                'label'     => __('Poll Answers'),
                'title'     => __('Poll Answers'),
                'content'   => $this->getLayout()->createBlock('Magento\Adminhtml\Block\Poll\Edit\Tab\Answers')
                    ->append($this->getLayout()->createBlock('Magento\Adminhtml\Block\Poll\Edit\Tab\Answers\ListAnswers'))
                    ->toHtml(),
                'active'    => ( $this->getRequest()->getParam('tab') == 'answers_section' ) ? true : false,
            ));
        return parent::_beforeToHtml();
    }
}
