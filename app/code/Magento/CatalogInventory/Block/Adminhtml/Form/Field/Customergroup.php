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
namespace Magento\CatalogInventory\Block\Adminhtml\Form\Field;

class Customergroup extends \Magento\Core\Block\Html\Select
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
     * @var \Magento\Customer\Model\Resource\Group\CollectionFactory
     */
    protected $_groupCollectionFactory;

    /**
     * Construct
     *
     * @param \Magento\Core\Block\Context $context
     * @param \Magento\Customer\Model\Resource\Group\CollectionFactory $groupCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Context $context,
        \Magento\Customer\Model\Resource\Group\CollectionFactory $groupCollectionFactory,
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
                /* @var $item \Magento\Customer\Model\Group */
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
                $this->addOption(\Magento\Customer\Model\Group::CUST_GROUP_ALL, __('ALL GROUPS'));
            }
            foreach ($this->_getCustomerGroups() as $groupId => $groupLabel) {
                $this->addOption($groupId, addslashes($groupLabel));
            }
        }
        return parent::_toHtml();
    }
}
