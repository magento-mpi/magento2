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
 */
namespace Magento\Catalog\Model\Product\Attribute\Source;
class Inputtype extends \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Registry $coreRegistry
    ) {
        $this->_eventManager = $eventManager;
        $this->_coreRegistry = $coreRegistry;
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

        $response = new \Magento\Object();
        $response->setTypes(array());
        $this->_eventManager->dispatch('adminhtml_product_attribute_types', array('response'=>$response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $inputTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
        }

        if ($this->_coreRegistry->registry('attribute_type_hidden_fields') === null) {
            $this->_coreRegistry->register('attribute_type_hidden_fields', $_hiddenFields);
        }
        return array_merge(parent::toOptionArray(), $inputTypes);
    }
}
