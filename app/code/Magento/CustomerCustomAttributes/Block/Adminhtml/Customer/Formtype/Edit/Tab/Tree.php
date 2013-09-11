<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Form Type Edit General Tab Block
 *
 * @category   Magento
 * @package    Magento_CustomerCustomAttributes
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Edit\Tab;

class Tree
    extends \Magento\Adminhtml\Block\Widget\Form
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    /**
     * Retrieve current form type instance
     *
     * @return \Magento\Eav\Model\Form\Type
     */
    protected function _getFormType()
    {
        return \Mage::registry('current_form_type');
    }

    public function getTreeButtonsHtml()
    {
        $addButtonData = array(
            'id'        => 'add_node_button',
            'label'     => __('New Fieldset'),
            'onclick'   => 'formType.newFieldset()',
            'class'     => 'add',
        );
        return $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Widget\Button')
            ->setData($addButtonData)->toHtml();
    }

    public function getFieldsetButtonsHtml()
    {
        $buttons = array();
        $buttons[] = $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Widget\Button')->setData(array(
            'id'        => 'save_node_button',
            'label'     => __('Save'),
            'onclick'   => 'formType.saveFieldset()',
            'class'     => 'save',
        ))->toHtml();
        $buttons[] = $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Widget\Button')->setData(array(
            'id'        => 'delete_node_button',
            'label'     => __('Remove'),
            'onclick'   => 'formType.deleteFieldset()',
            'class'     => 'delete',
        ))->toHtml();
        $buttons[] = $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Widget\Button')->setData(array(
            'id'        => 'cancel_node_button',
            'label'     => __('Cancel'),
            'onclick'   => 'formType.cancelFieldset()',
            'class'     => 'cancel',
        ))->toHtml();

        return join(' ', $buttons);
    }

    /**
     * Retrieve all store objects
     *
     * @return array
     */
    public function getStores()
    {
        if (!$this->hasData('stores')) {
            $this->setData('stores', \Mage::app()->getStores(false));
        }
        return $this->_getData('stores');
    }

    /**
     * Retrieve stores array in JSON format
     *
     * @return string
     */
    public function getStoresJson()
    {
        $result = array();
        $stores = $this->getStores();
        foreach ($stores as $stores) {
            $result[$stores->getId()] = $stores->getName();
        }

        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result);
    }

    /**
     * Retrieve form attributes JSON
     *
     * @return string
     */
    public function getAttributesJson()
    {
        $nodes = array();

        $fieldsetCollection = \Mage::getModel('\Magento\Eav\Model\Form\Fieldset')->getCollection()
            ->addTypeFilter($this->_getFormType())
            ->setSortOrder();
        $elementCollection = \Mage::getModel('\Magento\Eav\Model\Form\Element')->getCollection()
            ->addTypeFilter($this->_getFormType())
            ->setSortOrder();
        foreach ($fieldsetCollection as $fieldset) {
            /* @var $fieldset \Magento\Eav\Model\Form\Fieldset */
            $node = array(
                'node_id'   => $fieldset->getId(),
                'parent'    => null,
                'type'      => 'fieldset',
                'code'      => $fieldset->getCode(),
                'label'     => $fieldset->getLabel()
            );

            foreach ($fieldset->getLabels() as $storeId => $label) {
                $node['label_' . $storeId] = $label;
            }

            $nodes[] = $node;
        }

        foreach ($elementCollection as $element) {
            /* @var $element \Magento\Eav\Model\Form\Element */
            $nodes[] = array(
                'node_id'   => 'a_' . $element->getId(),
                'parent'    => $element->getFieldsetId(),
                'type'      => 'element',
                'code'      => $element->getAttribute()->getAttributeCode(),
                'label'     => $element->getAttribute()->getFrontend()->getLabel()
            );
        }

        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($nodes);
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Attributes');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Attributes');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
