<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export customer finance entity model
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection
    extends Varien_Data_Collection
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
     * @var Mage_Eav_Model_AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * Class constructor
     * @param Mage_Eav_Model_AttributeFactory $attributeFactory
     */
    public function __construct(Mage_Eav_Model_AttributeFactory $attributeFactory)
    {
        $this->_attributeFactory = $attributeFactory;

        /** @var $helper Enterprise_ImportExport_Helper_Data */
        $helper = Mage::helper('Enterprise_ImportExport_Helper_Data');

        if ($helper->isCustomerBalanceEnabled()) {
            $storeCreditData = array(
                'attribute_id'   => self::CUSTOMER_ENTITY_FINANCE_ATTRIBUTE_CUSTOMER_BALANCE,
                'attribute_code' => self::COLUMN_CUSTOMER_BALANCE,
                'frontend_label' => __('Store Credit'),
                'backend_type'   => 'decimal',
                'is_required'    => false,
            );
            $this->addItem(
                $this->_attributeFactory->createAttribute('Mage_Eav_Model_Entity_Attribute', $storeCreditData)
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
                $this->_attributeFactory->createAttribute('Mage_Eav_Model_Entity_Attribute', $rewardPointsData)
            );
        }
    }

    /**
     * Add select order
     *
     * @param  string $field
     * @param  string $direction
     * @return Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection
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
     * @param Varien_Object $a
     * @param Varien_Object $b
     * @return int
     */
    public function compareAttributes(Varien_Object $a, Varien_Object $b)
    {
        return strnatcmp($a->getData($this->_orderField), $b->getData($this->_orderField));
    }
}
