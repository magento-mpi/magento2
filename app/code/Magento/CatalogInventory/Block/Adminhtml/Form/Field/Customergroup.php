<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * HTML select element block with customer groups options
 */
class Magento_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup extends Magento_Core_Block_Html_Select
{
    /**
     * Customer groups cache
     *
     * @var array
     */
    private $_customerGroups;

    /**
     * Flag whether to add group all option or no
     *
     * @var bool
     */
    protected $_addGroupAllOption = true;

    /**
     * Customer group collection factory
     *
     * @var Magento_Customer_Model_Resource_Group_CollectionFactory
     */
    protected $_groupCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Block_Context $context
     * @param Magento_Customer_Model_Resource_Group_CollectionFactory $groupCollectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Context $context,
        Magento_Customer_Model_Resource_Group_CollectionFactory $groupCollectionFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * Retrieve allowed customer groups
     *
     * @param int $groupId  return name by customer group id
     * @return array|string
     */
    protected function _getCustomerGroups($groupId = null)
    {
        if (is_null($this->_customerGroups)) {
            $this->_customerGroups = array();
            foreach ($this->_groupCollectionFactory->create() as $item) {
                /* @var $item Magento_Customer_Model_Group */
                $this->_customerGroups[$item->getId()] = $item->getCustomerGroupCode();
            }
        }
        if (!is_null($groupId)) {
            return isset($this->_customerGroups[$groupId]) ? $this->_customerGroups[$groupId] : null;
        }
        return $this->_customerGroups;
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            if ($this->_addGroupAllOption) {
                $this->addOption(Magento_Customer_Model_Group::CUST_GROUP_ALL, __('ALL GROUPS'));
            }
            foreach ($this->_getCustomerGroups() as $groupId => $groupLabel) {
                $this->addOption($groupId, addslashes($groupLabel));
            }
        }
        return parent::_toHtml();
    }
}
