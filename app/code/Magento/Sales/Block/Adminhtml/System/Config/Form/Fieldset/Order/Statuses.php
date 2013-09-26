<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Block_Adminhtml_System_Config_Form_Fieldset_Order_Statuses
    extends Magento_Backend_Block_System_Config_Form_Fieldset
{
    /**
     * @var Magento_Object
     */
    protected $_dummyElement;

    /**
     * @var Magento_Backend_Block_System_Config_Form_Field
     */
    protected $_fieldRenderer;

    /**
     * @var array
     */
    protected $_values;

    /**
     * @var Magento_Sales_Model_Resource_Order_Status_CollectionFactory
     */
    protected $_orderStatusCollection;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Sales_Model_Resource_Order_Status_CollectionFactory $orderStatusCollection
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Sales_Model_Resource_Order_Status_CollectionFactory $orderStatusCollection,
        array $data = array()
    ) {
        $this->_orderStatusCollection = $orderStatusCollection;
        parent::__construct($context, $data);
    }

    /**
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $html = '';

        $statuses = $this->_orderStatusCollection->create()->load()->toOptionHash();

        foreach ($statuses as $id => $status) {
            $html.= $this->_getFieldHtml($element, $id, $status);
        }
        return $html;
    }

    /**
     * @return Magento_Object
     */
    protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new Magento_Object(array('showInDefault' => 1, 'showInWebsite' => 1));
        }
        return $this->_dummyElement;
    }

    /**
     * @return Magento_Backend_Block_System_Config_Form_Field
     */
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = $this->getLayout()
                ->getBlockSingleton('Magento_Backend_Block_System_Config_Form_Field');
        }
        return $this->_fieldRenderer;
    }

    /**
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param string $id
     * @param string $status
     * @return string
     */
    protected function _getFieldHtml($fieldset, $id, $status)
    {
        $configData = $this->getConfigData();
        $path = 'sales/order_statuses/status_'.$id; //TODO: move as property of form
        $data = isset($configData[$path]) ? $configData[$path] : array();

        $e = $this->_getDummyElement();

        $field = $fieldset->addField($id, 'text',
            array(
                'name'          => 'groups[order_statuses][fields][status_'.$id.'][value]',
                'label'         => $status,
                'value'         => isset($data['value']) ? $data['value'] : $status,
                'default_value' => isset($data['default_value']) ? $data['default_value'] : '',
                'old_value'     => isset($data['old_value']) ? $data['old_value'] : '',
                'inherit'       => isset($data['inherit']) ? $data['inherit'] : '',
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),
            ))->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
}
