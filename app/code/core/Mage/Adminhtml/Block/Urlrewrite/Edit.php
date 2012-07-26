<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for URL rewrites edit page
 *
 * @method Mage_Core_Model_Url_Rewrite getUrlRewrite()
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Edit extends Mage_Adminhtml_Block_Widget_Container
{
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
     * @return Mage_Adminhtml_Block_Urlrewrite_Edit
     */
    protected function _prepareLayout()
    {
        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');

        $this->setTemplate('urlrewrite/edit.phtml');

        $this->_addButton('back', array(
            'label'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Back'),
            'onclick' => 'setLocation(\'' . $helper->getUrl('*/*/') . '\')',
            'class'   => 'back',
            'level'   => -1
        ));

        $this->_prepareLayoutFeatures();

        return parent::_prepareLayout();
    }

    /**
     * Prepare featured blocks for layout of URL rewrite editing
     */
    protected function _prepareLayoutFeatures()
    {
        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');

        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Edit URL Rewrite');

        $this->_addButton('reset', array(
            'label'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Reset'),
            'onclick' => '$(\'edit_form\').reset()',
            'class'   => 'scalable',
            'level'   => -1
        ));

        $this->_addButton('delete', array(
            'label'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Delete'),
            'onclick' => 'deleteConfirm(\''
                . Mage::helper('Mage_Adminhtml_Helper_Data')->__('Are you sure you want to do this?')
                . '\', \'' . $helper->getUrl('*/*/delete', array('id' => $this->getUrlRewrite()->getId())) . '\')',
            'class'   => 'scalable delete',
            'level'   => -1
        ));

        $this->_addEditFormBlock();
    }

    /**
     * Add child edit form block
     */
    protected function _addEditFormBlock()
    {
        $this->setChild('form', $this->_createEditFormBlock());

        $this->_addButton('save', array(
            'label'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save'),
            'onclick' => 'editForm.submit()',
            'class'   => 'save',
            'level'   => -1
        ));
    }

    /**
     * Creates edit form block
     *
     * @return Mage_Adminhtml_Block_Urlrewrite_Edit_Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Edit_Form', '', array(
            'url_rewrite' => $this->getUrlRewrite()
        ));
    }

    /**
     * Add child URL rewrite selector block
     */
    protected function _addUrlRewriteSelectorBlock()
    {
        $this->setChild('selector', $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Selector'));
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
}
