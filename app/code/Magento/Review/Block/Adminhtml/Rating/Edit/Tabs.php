<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Adminhtml\Rating\Edit;

/**
 * Admin rating left menu
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rating_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Rating Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_section',
            array(
                'label' => __('Rating Information'),
                'title' => __('Rating Information'),
                'content' => $this->getLayout()
                        ->createBlock('Magento\Review\Block\Adminhtml\Rating\Edit\Tab\Form')
                        ->toHtml()
            )
        );
        return parent::_beforeToHtml();
    }
}
