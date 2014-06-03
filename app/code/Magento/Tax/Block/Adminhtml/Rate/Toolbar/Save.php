<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin tax rate save toolbar
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Block\Adminhtml\Rate\Toolbar;

class Save extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'toolbar/rate/save.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->assign('createUrl', $this->getUrl('tax/rate/save'));
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'backButton',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Back'),
                'onclick' => 'window.location.href=\'' . $this->getUrl('tax/*/') . '\'',
                'class' => 'back'
            )
        );

        $this->getToolbar()->addChild(
            'resetButton',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Reset'), 'onclick' => 'window.location.reload()', 'class' => 'reset')
        );

        $rate = intval($this->getRequest()->getParam('rate'));
        if ($rate) {
            $this->getToolbar()->addChild(
                'deleteButton',
                'Magento\Backend\Block\Widget\Button',
                array(
                    'label' => __('Delete Rate'),
                    'onclick' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getUrl(
                        'tax/*/delete',
                        array('rate' => $rate)
                    ) . '\')',
                    'class' => 'delete'
                )
            );
        }

        $this->getToolbar()->addChild(
            'saveButton',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Save Rate'),
                'class' => 'save primary save-rate',
                'data_attribute' => array(
                    'mage-init' => array('button' => array('event' => 'save', 'target' => '#rate-form'))
                )
            )
        );

        return parent::_prepareLayout();
    }
}
