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

class Customergroup extends \Magento\Framework\View\Element\Html\Select
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
     * Customer group service
     *
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_groupService = $groupService;
    }

    /**
     * Retrieve allowed customer groups
     *
     * @param int $groupId return name by customer group id
     * @return array|string
     */
    protected function _getCustomerGroups($groupId = null)
    {
        if (is_null($this->_customerGroups)) {
            $this->_customerGroups = array();
            foreach ($this->_groupService->getGroups() as $item) {
                /* @var $item \Magento\Customer\Service\V1\Data\CustomerGroup */
                $this->_customerGroups[$item->getId()] = $item->getCode();
            }
        }
        if (!is_null($groupId)) {
            return isset($this->_customerGroups[$groupId]) ? $this->_customerGroups[$groupId] : null;
        }
        return $this->_customerGroups;
    }

    /**
     * @param string $value
     * @return $this
     */
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
                $this->addOption(
                    \Magento\Customer\Service\V1\CustomerGroupServiceInterface::CUST_GROUP_ALL,
                    __('ALL GROUPS')
                );
            }
            foreach ($this->_getCustomerGroups() as $groupId => $groupLabel) {
                $this->addOption($groupId, addslashes($groupLabel));
            }
        }
        return parent::_toHtml();
    }
}
