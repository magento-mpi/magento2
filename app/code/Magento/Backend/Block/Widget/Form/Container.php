<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend form container block
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\Widget\Form;

class Container extends \Magento\Backend\Block\Widget\Container
{
    protected $_objectId = 'id';
    protected $_formScripts = array();
    protected $_formInitScripts = array();
    protected $_mode = 'edit';
    protected $_blockGroup = 'Magento_Backend';

    protected $_template = 'Magento_Backend::widget/form/container.phtml';


    protected function _construct()
    {
        parent::_construct();

        $this->_addButton('back', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class'     => 'back',
        ), -1);
        $this->_addButton('reset', array(
            'label'     => __('Reset'),
            'onclick'   => 'setLocation(window.location.href)',
        ), -1);

        $objId = $this->getRequest()->getParam($this->_objectId);

        if (! empty($objId)) {
            $this->_addButton('delete', array(
                'label'     => __('Delete'),
                'class'     => 'delete',
                'onclick'   => 'deleteConfirm(\'' . __('Are you sure you want to do this?')
                    . '\', \'' . $this->getDeleteUrl() . '\')',
            ));
        }

        $this->_addButton('save', array(
            'label'     => __('Save'),
            'class'     => 'save primary',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#edit_form'),
                ),
            ),
        ), 1);
    }

    /**
     * Create form block
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if ($this->_blockGroup && $this->_controller && $this->_mode
            && !$this->_layout->getChildName($this->_nameInLayout, 'form')
        ) {
            $this->addChild('form', $this->_buildFormClassName());
        }
        return parent::_prepareLayout();
    }

    /**
     * Build child form class name
     *
     * @return string
     */
    protected function _buildFormClassName()
    {
        return $this->_blockGroup
            . '_Block_'
            . str_replace(' ', '_', ucwords(str_replace('_', ' ', $this->_controller . '_' . $this->_mode)))
            . '_Form';
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    /**
     * Get form save URL
     *
     * @see getFormActionUrl()
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getFormActionUrl();
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl('*/' . $this->_controller . '/save');
    }

    public function getFormHtml()
    {
        $this->getChildBlock('form')->setData('action', $this->getSaveUrl());
        return $this->getChildHtml('form');
    }

    public function getFormInitScripts()
    {
        if ( !empty($this->_formInitScripts) && is_array($this->_formInitScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formInitScripts) . '</script>';
        }
        return '';
    }

    public function getFormScripts()
    {
        if ( !empty($this->_formScripts) && is_array($this->_formScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formScripts) . '</script>';
        }
        return '';
    }

    public function getHeaderWidth()
    {
        return '';
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-' . strtr($this->_controller, '_', '-');
    }

    public function getHeaderHtml()
    {
        return '<h3 class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }

    /**
     * Set data object and pass it to form
     *
     * @param \Magento\Object $object
     * @return \Magento\Backend\Block\Widget\Form\Container
     */
    public function setDataObject($object)
    {
        $this->getChildBlock('form')->setDataObject($object);
        return $this->setData('data_object', $object);
    }
}
