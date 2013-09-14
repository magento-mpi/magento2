<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export customer finance entity model
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance;

class Collection
    extends \Magento\Data\Collection
{
    /**#@+
     * Customer entity finance attribute ids
     */
    const CUSTOMER_ENTITY_FINANCE_ATTRIBUTE_CUSTOMER_BALANCE = 1;
    const CUSTOMER_ENTITY_FINANCE_ATTRIBUTE_REWARD_POINTS    = 2;
    /**#@-*/

    /**#@+
     * Column names
     */
    const COLUMN_CUSTOMER_BALANCE = 'store_credit';
    const COLUMN_REWARD_POINTS    = 'reward_points';
    /**#@-*/

    /** @var string */
    protected $_orderField;

    /**
     * @var \Magento\Eav\Model\AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * Class constructor
     * @param \Magento\Eav\Model\AttributeFactory $attributeFactory
     */
    public function __construct(\Magento\Eav\Model\AttributeFactory $attributeFactory)
    {
        $this->_attributeFactory = $attributeFactory;

        /** @var $helper \Magento\ScheduledImportExport\Helper\Data */
        $helper = \Mage::helper('Magento\ScheduledImportExport\Helper\Data');

        if ($helper->isCustomerBalanceEnabled()) {
            $storeCreditData = array(
                'attribute_id'   => self::CUSTOMER_ENTITY_FINANCE_ATTRIBUTE_CUSTOMER_BALANCE,
                'attribute_code' => self::COLUMN_CUSTOMER_BALANCE,
                'frontend_label' => __('Store Credit'),
                'backend_type'   => 'decimal',
                'is_required'    => false,
            );
            $this->addItem(
                $this->_attributeFactory->createAttribute('Magento\Eav\Model\Entity\Attribute', $storeCreditData)
            );
        }

        if ($helper->isRewardPointsEnabled()) {
            $rewardPointsData = array(
                'attribute_id'   => self::CUSTOMER_ENTITY_FINANCE_ATTRIBUTE_REWARD_POINTS,
                'attribute_code' => self::COLUMN_REWARD_POINTS,
                'frontend_label' => __('Reward Points'),
                'backend_type'   => 'int',
                'is_required'    => false,
            );
            $this->addItem(
                $this->_attributeFactory->createAttribute('Magento\Eav\Model\Entity\Attribute', $rewardPointsData)
            );
        }
    }

    /**
     * Add select order
     *
     * @param  string $field
     * @param  string $direction
     * @return \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->_orderField = $field;
        uasort($this->_items, array($this, 'compareAttributes'));

        if ($direction == self::SORT_ORDER_DESC) {
            $this->_items = array_reverse($this->_items, true);
        }

        return $this;
    }

    /**
     * Compare two collection items
     *
     * @param \Magento\Object $a
     * @param \Magento\Object $b
     * @return int
     */
    public function compareAttributes(\Magento\Object $a, \Magento\Object $b)
    {
        return strnatcmp($a->getData($this->_orderField), $b->getData($this->_orderField));
    }
}
