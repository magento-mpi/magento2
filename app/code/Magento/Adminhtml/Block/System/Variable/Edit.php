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
 * Custom Variable Edit Container
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\System\Variable;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'variable_id';
        $this->_controller = 'system_variable';

        parent::_construct();
    }

    /**
     * Getter
     *
     * @return \Magento\Core\Model\Variable
     */
    public function getVariable()
    {
        return \Mage::registry('current_variable');
    }

    /**
     * Prepare layout.
     * Adding save_and_continue button
     *
     * @return \Magento\Adminhtml\Block\System\Variable\Edit
     */
    protected function _preparelayout()
    {
        $this->_addButton('save_and_edit', array(
            'label'     => __('Save and Continue Edit'),
            'class'     => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 100);
        if (!$this->getVariable()->getId()) {
            $this->removeButton('delete');
        }
        return parent::_prepareLayout();
    }

    /**
     * Return form HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        $formHtml = parent::getFormHtml();
        if (!\Mage::app()->isSingleStoreMode() && $this->getVariable()->getId()) {
            $storeSwitcher = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher')->toHtml();
            $formHtml = $storeSwitcher.$formHtml;
        }
        return $formHtml;
    }

    /**
     * Return translated header text depending on creating/editing action
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getVariable()->getId()) {
            return __('Custom Variable "%1"', $this->escapeHtml($this->getVariable()->getName()));
        }
        else {
            return __('New Custom Variable');
        }
    }

    /**
     * Return validation url for edit form
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true, 'back' => null));
    }

    /**
     * Return save and continue url for edit form
     *
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true, 'back' => 'edit'));
    }
}
