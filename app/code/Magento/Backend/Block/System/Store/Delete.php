<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\System\Store;

/**
 * Store / store view / website delete form container
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Delete extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'item_id';
        $this->_mode = 'delete';
        $this->_blockGroup = 'Magento_Backend';
        $this->_controller = 'system_store';

        parent::_construct();

        $this->_removeButton('save');
        $this->_removeButton('reset');

        $this->_updateButton('delete', 'region', 'footer');
        $this->_updateButton('delete', 'onclick', null);
        $this->_updateButton('delete', 'data_attribute',
            array('mage-init' => array(
                'button' => array('event' => 'save', 'target' => '#edit_form'),
            ))
        );

        $this->_addButton('cancel', array(
            'label'     => __('Cancel'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
        ), 2, 100, 'footer');

    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __("Delete %1 '%2'", $this->getStoreTypeTitle(),
            $this->escapeHtml($this->getChildBlock('form')->getDataObject()->getName()));
    }

    /**
     * Set store type title
     *
     * @param string $title
     * @return $this
     */
    public function setStoreTypeTitle($title)
    {
        $this->_updateButton('delete', 'label', __('Delete %1', $title));
        return $this->setData('store_type_title', $title);
    }

    /**
     * Set back URL for "Cancel" and "Back" buttons
     *
     * @param string $url
     * @return $this
     */
    public function setBackUrl($url)
    {
        $this->setData('back_url', $url);
        $this->_updateButton('cancel', 'onclick', "setLocation('" . $url . "')");
        $this->_updateButton('back', 'onclick', "setLocation('" . $url . "')");
        return $this;
    }

}
