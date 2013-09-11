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
 * Block for URL rewrites edit page
 *
 * @method \Magento\Core\Model\Url\Rewrite getUrlRewrite()
 * @method \Magento\Adminhtml\Block\Urlrewrite\Edit setUrlRewrite(\Magento\Core\Model\Url\Rewrite $urlRewrite)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Urlrewrite;

class Edit extends \Magento\Adminhtml\Block\Widget\Container
{
    /**
     * @var \Magento\Adminhtml\Block\Urlrewrite\Selector
     */
    private $_selectorBlock;

    /**
     * Part for building some blocks names
     *
     * @var string
     */
    protected $_controller = 'urlrewrite';

    /**
     * Generated buttons html cache
     *
     * @var string
     */
    protected $_buttonsHtml;

    /**
     * Prepare URL rewrite editing layout
     *
     * @return \Magento\Adminhtml\Block\Urlrewrite\Edit
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('urlrewrite/edit.phtml');

        $this->_addBackButton();
        $this->_prepareLayoutFeatures();

        return parent::_prepareLayout();
    }

    /**
     * Prepare featured blocks for layout of URL rewrite editing
     */
    protected function _prepareLayoutFeatures()
    {
        /** @var $helper \Magento\Adminhtml\Helper\Data */
        $helper = \Mage::helper('Magento\Adminhtml\Helper\Data');

        if ($this->_getUrlRewrite()->getId()) {
            $this->_headerText = __('Edit URL Rewrite');
        } else {
            $this->_headerText = __('Add New URL Rewrite');
        }

        $this->_updateBackButtonLink($helper->getUrl('*/*/edit') . $this->_getSelectorBlock()->getDefaultMode());
        $this->_addUrlRewriteSelectorBlock();
        $this->_addEditFormBlock();
    }

    /**
     * Add child edit form block
     */
    protected function _addEditFormBlock()
    {
        $this->setChild('form', $this->_createEditFormBlock());

        if ($this->_getUrlRewrite()->getId()) {
            $this->_addResetButton();
            $this->_addDeleteButton();
        }

        $this->_addSaveButton();
    }

    /**
     * Add reset button
     */
    protected function _addResetButton()
    {
        $this->_addButton('reset', array(
            'label'   => __('Reset'),
            'onclick' => '$(\'edit_form\').reset()',
            'class'   => 'scalable',
            'level'   => -1
        ));
    }

    /**
     * Add back button
     */
    protected function _addBackButton()
    {
        /** @var $helper \Magento\Adminhtml\Helper\Data */
        $helper = \Mage::helper('Magento\Adminhtml\Helper\Data');

        $this->_addButton('back', array(
            'label'   => __('Back'),
            'onclick' => 'setLocation(\'' . $helper->getUrl('*/*/') . '\')',
            'class'   => 'back',
            'level'   => -1
        ));
    }

    /**
     * Update Back button location link
     *
     * @param string $link
     */
    protected function _updateBackButtonLink($link)
    {
        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $link . '\')');
    }

    /**
     * Add delete button
     */
    protected function _addDeleteButton()
    {
        /** @var $helper \Magento\Adminhtml\Helper\Data */
        $helper = \Mage::helper('Magento\Adminhtml\Helper\Data');

        $this->_addButton('delete', array(
            'label'   => __('Delete'),
            'onclick' => 'deleteConfirm(\''
                . addslashes(__('Are you sure you want to do this?'))
                . '\', \'' . $helper->getUrl('*/*/delete', array('id' => $this->getUrlRewrite()->getId())) . '\')',
            'class'   => 'scalable delete',
            'level'   => -1
        ));
    }

    /**
     * Add save button
     */
    protected function _addSaveButton()
    {
        $this->_addButton('save', array(
            'label'   => __('Save'),
            'class'   => 'save',
            'level'   => -1,
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#edit_form'),
                ),
            ),
        ));
    }

    /**
     * Creates edit form block
     *
     * @return \Magento\Adminhtml\Block\Urlrewrite\Edit\Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Urlrewrite\Edit\Form', '',
            array('data' => array(
                'url_rewrite' => $this->_getUrlRewrite()
            ))
        );
    }

    /**
     * Add child URL rewrite selector block
     */
    protected function _addUrlRewriteSelectorBlock()
    {
        $this->setChild('selector', $this->_getSelectorBlock());
    }

    /**
     * Get selector block
     *
     * @return \Magento\Adminhtml\Block\Urlrewrite\Selector
     */
    private function _getSelectorBlock()
    {
        if (!$this->_selectorBlock) {
            $this->_selectorBlock = $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Urlrewrite\Selector');
        }
        return $this->_selectorBlock;
    }

    /**
     * Get container buttons HTML
     *
     * Since buttons are set as children, we remove them as children after generating them
     * not to duplicate them in future
     *
     * @param null $area
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getButtonsHtml($area = null)
    {
        if (null === $this->_buttonsHtml) {
            $this->_buttonsHtml = parent::getButtonsHtml();
            $layout = $this->getLayout();
            foreach ($this->getChildNames() as $name) {
                $alias = $layout->getElementAlias($name);
                if (false !== strpos($alias, '_button')) {
                    $layout->unsetChild($this->getNameInLayout(), $alias);
                }
            }
        }
        return $this->_buttonsHtml;
    }

    /**
     * Get or create new instance of URL rewrite
     *
     * @return \Magento\Core\Model\Url\Rewrite
     */
    protected function _getUrlRewrite()
    {
        if (!$this->hasData('url_rewrite')) {
            $this->setUrlRewrite(\Mage::getModel('\Magento\Core\Model\Url\Rewrite'));
        }
        return $this->getUrlRewrite();
    }
}
