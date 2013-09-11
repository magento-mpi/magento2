<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\System\Design\Edit;

class Tabs extends \Magento\Adminhtml\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('design_tabs');
        $this->setDestElementId('design-edit-form');
        $this->setTitle(__('Design Change'));
    }

    protected function _prepareLayout()
    {
        $this->addTab('general', array(
            'label'     => __('General'),
            'content'   => $this->getLayout()
                ->createBlock('\Magento\Adminhtml\Block\System\Design\Edit\Tab\General')->toHtml(),
        ));

        return parent::_prepareLayout();
    }
}
