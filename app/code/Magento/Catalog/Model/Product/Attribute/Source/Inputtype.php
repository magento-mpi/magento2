<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attribute source input types
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Source_Inputtype
    extends Magento_Eav_Model_Adminhtml_System_Config_Source_Inputtype
{
    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager_Proxy
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager
    ) {
        $this->_eventManager = $eventManager;
    }

    /**
     * Get product input types as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $inputTypes = array(
            array(
                'value' => 'price',
                'label' => __('Price')
            ),
            array(
                'value' => 'media_image',
                'label' => __('Media Image')
            )
        );

        $response = new Magento_Object();
        $response->setTypes(array());
        $this->_eventManager->dispatch('adminhtml_product_attribute_types', array('response'=>$response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $inputTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }

        if (Mage::registry('attribute_type_hidden_fields') === null) {
            Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        }
        if (Mage::registry('attribute_type_disabled_types') === null) {
            Mage::register('attribute_type_disabled_types', $_disabledTypes);
        }

        return array_merge(parent::toOptionArray(), $inputTypes);
    }
}
