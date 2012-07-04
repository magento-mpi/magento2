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
 * Import customer finance entity model
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method      array getData() getData()
 */
class Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance
    extends Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract
{
    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL           = 'email';
    const COLUMN_WEBSITE         = '_website';
    const COLUMN_FINANCE_WEBSITE = '_finance_website';
    /**#@-*/

    /**#@+
     * Error codes
     */
    const ERROR_FINANCE_WEBSITE_IS_EMPTY = 'financeWebsiteIsEmpty';
    const ERROR_INVALID_FINANCE_WEBSITE  = 'invalidFinanceWebsite';
    /**#@-*/

    /**
     * Permanent entity columns
     *
     * @var array
     */
    protected $_permanentAttributes = array(self::COLUMN_WEBSITE, self::COLUMN_EMAIL);

    /**
     * Column names that holds values with particular meaning
     *
     * @var array
     */
    protected $_particularAttributes = array(
        self::COLUMN_WEBSITE,
        self::COLUMN_EMAIL,
        self::COLUMN_FINANCE_WEBSITE,
    );

    /**
     * Comment for finance data import
     *
     * @var string
     */
    protected $_comment;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        /** @var $helper Enterprise_ImportExport_Helper_Data */
        $helper = Mage::helper('Enterprise_ImportExport_Helper_Data');

        $this->addMessageTemplate(self::ERROR_FINANCE_WEBSITE_IS_EMPTY,
            $helper->__('Finance information website is not specified')
        );
        $this->addMessageTemplate(self::ERROR_INVALID_FINANCE_WEBSITE,
            $helper->__('Invalid value in Finance information website column (website does not exists?)')
        );

        $this->_initAttributes();
    }

    /**
     * Initialize entity attributes
     *
     * @return Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract
     */
    protected function _initAttributes()
    {
        $collection = $this->_getAttributeCollection();
        /** @var $attribute Mage_Eav_Model_Attribute */
        foreach ($collection as $attribute) {
            $this->_attributes[$attribute->getAttributeCode()] = array(
                'id'          => $attribute->getId(),
                'code'        => $attribute->getAttributeCode(),
                'is_required' => $attribute->getIsRequired(),
                'type'        => $attribute->getBackendType(),
            );
        }
        return $this;
    }

    /**
     * Import data rows
     *
     * @return boolean
     */
    protected function _importData()
    {
        /** @var $importExportHelper Enterprise_ImportExport_Helper_Data */
        $importExportHelper = Mage::helper('Enterprise_ImportExport_Helper_Data');
        if (!$importExportHelper->isRewardPointsEnabled() && !$importExportHelper->isCustomerBalanceEnabled()) {
            return false;
        }

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $rewardPointsKey =
            Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_REWARD_POINTS;
        $customerBalanceKey =
            Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_CUSTOMER_BALANCE;

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNumber => $rowData) {
                // check row data
                if (!$this->validateRow($rowData, $rowNumber)) {
                    continue;
                }
                // load customer object
                $customerId = $this->_getCustomerId(
                    $rowData[self::COLUMN_EMAIL],
                    $rowData[self::COLUMN_WEBSITE]
                );
                if ($customer->getId() != $customerId) {
                    $customer->reset();
                    $customer->load($customerId);
                }

                // get website for finance data ID
                $websiteId = null;
                if (!empty($rowData[self::COLUMN_FINANCE_WEBSITE])) {
                    $websiteId = $this->_websiteCodeToId[$rowData[self::COLUMN_FINANCE_WEBSITE]];
                }

                // save finance data for customer
                foreach ($this->_attributes as $attributeCode => $attributeParams) {
                    if ($this->getBehavior($rowData) == Mage_ImportExport_Model_Import::BEHAVIOR_V2_DELETE) {
                        if ($attributeCode == $rewardPointsKey) {
                            $this->_deleteRewardPoints($customer, $websiteId);
                        } elseif ($attributeCode == $customerBalanceKey) {
                            $this->_deleteCustomerBalance($customer, $websiteId);
                        }
                    } else {
                        if (isset($rowData[$attributeCode]) && strlen($rowData[$attributeCode])) {
                            if ($attributeCode == $rewardPointsKey) {
                                $this->_updateRewardPointsForCustomer(
                                    $customer, $websiteId, $rowData[$attributeCode]
                                );
                            } elseif ($attributeCode == $customerBalanceKey) {
                                $this->_updateCustomerBalanceForCustomer(
                                    $customer, $websiteId, $rowData[$attributeCode]
                                );
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Update reward points value for customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param int $websiteId
     * @param int $value reward points value
     * @return Enterprise_Reward_Model_Reward
     */
    protected function _updateRewardPointsForCustomer(Mage_Customer_Model_Customer $customer, $websiteId, $value)
    {
        /** @var $rewardModel Enterprise_Reward_Model_Reward */
        $rewardModel = Mage::getModel('Enterprise_Reward_Model_Reward');
        $rewardModel->setCustomer($customer)
            ->setWebsiteId($websiteId)
            ->loadByCustomer();

        return $this->_updateRewardValue($rewardModel, $value);
    }

    /**
     * Update reward points value for reward model
     *
     * @param Enterprise_Reward_Model_Reward $rewardModel
     * @param int $value reward points value
     * @return Enterprise_Reward_Model_Reward
     */
    protected function _updateRewardValue(Enterprise_Reward_Model_Reward $rewardModel, $value)
    {
        $pointsDelta = $value - $rewardModel->getPointsBalance();
        if ($pointsDelta != 0) {
            $rewardModel->setPointsDelta($pointsDelta)
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_ADMIN)
                ->setComment($this->_getComment())
                ->updateRewardPoints();
        }

        return $rewardModel;
    }

    /**
     * Update store credit balance for customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param int $websiteId
     * @param float $value store credit balance
     * @return Enterprise_CustomerBalance_Model_Balance
     */
    protected function _updateCustomerBalanceForCustomer(Mage_Customer_Model_Customer $customer, $websiteId, $value)
    {
        /** @var $balanceModel Enterprise_CustomerBalance_Model_Balance */
        $balanceModel = Mage::getModel('Enterprise_CustomerBalance_Model_Balance');
        $balanceModel->setCustomer($customer)
            ->setWebsiteId($websiteId)
            ->loadByCustomer();

        return $this->_updateCustomerBalanceValue($balanceModel, $value);
    }

    /**
     * Update balance for customer balance model
     *
     * @param Enterprise_CustomerBalance_Model_Balance $balanceModel
     * @param float $value store credit balance
     * @return Enterprise_CustomerBalance_Model_Balance
     */
    protected function _updateCustomerBalanceValue(Enterprise_CustomerBalance_Model_Balance $balanceModel, $value)
    {
        $amountDelta = $value - $balanceModel->getAmount();
        if ($amountDelta != 0) {
            $balanceModel->setAmountDelta($amountDelta)
                ->setComment($this->_getComment())
                ->save();
        }

        return $balanceModel;
    }

    /**
     * Delete reward points value for customer (just set it to 0)
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param int|null $websiteId
     */
    protected function _deleteRewardPoints(Mage_Customer_Model_Customer $customer, $websiteId = null)
    {
        if (is_null($websiteId)) {
            /** @var $rewardModel Enterprise_Reward_Model_Reward */
            $rewardModel = Mage::getModel('Enterprise_Reward_Model_Reward');

            $customerRewards = $rewardModel
                ->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId());

            foreach ($customerRewards as $rewardEntity) {
                $this->_updateRewardValue($rewardEntity, 0);
            }
        } else {
            $this->_updateRewardPointsForCustomer($customer, $websiteId, 0);
        }
    }

    /**
     * Delete store credit balance for customer (just set it to 0)
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param int|null $websiteId
     */
    protected function _deleteCustomerBalance(Mage_Customer_Model_Customer $customer, $websiteId = null)
    {
        if (is_null($websiteId)) {
            /** @var $rewardModel Enterprise_Reward_Model_Reward */
            $customerBalanceModel = Mage::getModel('Enterprise_CustomerBalance_Model_Balance');

            $customerBalances = $customerBalanceModel
                ->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId());

            foreach ($customerBalances as $customerBalanceEntity) {
                $this->_updateCustomerBalanceValue($customerBalanceEntity, 0);
            }
        } else {
            $this->_updateCustomerBalanceForCustomer($customer, $websiteId, 0);
        }
    }

    /**
     * Retrieve comment string
     *
     * @return string
     */
    protected function _getComment()
    {
        if (!$this->_comment) {
            /** @var $helper Enterprise_ImportExport_Helper_Data */
            $helper = Mage::helper('Enterprise_ImportExport_Helper_Data');
            /* @var $adminUser Mage_User_Model_User */
            $adminUser = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser();
            $this->_comment = $helper->__('Data was imported by %s', $adminUser->getUsername());
        }

        return $this->_comment;
    }

    /**
     * Imported entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customer_finance';
    }

    /**
     * Validate data row for add/update behaviour
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return null
     */
    protected function _validateRowForUpdate(array $rowData, $rowNumber)
    {
        if ($this->_checkUniqueKey($rowData, $rowNumber)) {
            if (empty($rowData[self::COLUMN_FINANCE_WEBSITE])) {
                $this->addRowError(self::ERROR_FINANCE_WEBSITE_IS_EMPTY, $rowNumber, self::COLUMN_FINANCE_WEBSITE);
            } else {
                $email   = strtolower($rowData[self::COLUMN_EMAIL]);
                $website = $rowData[self::COLUMN_WEBSITE];
                $financeWebsite = $rowData[self::COLUMN_FINANCE_WEBSITE];

                if (!isset($this->_websiteCodeToId[$financeWebsite])
                    || $this->_websiteCodeToId[$financeWebsite] == Mage_Core_Model_App::ADMIN_STORE_ID
                ) {
                    $this->addRowError(self::ERROR_INVALID_FINANCE_WEBSITE, $rowNumber, self::COLUMN_FINANCE_WEBSITE);
                } elseif (!$this->_getCustomerId($email, $website)) {
                    $this->addRowError(self::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
                } else {
                    // check simple attributes
                    foreach ($this->_attributes as $attributeCode => $attributeParams) {
                        if (in_array($attributeCode, $this->_ignoredAttributes)) {
                            continue;
                        }
                        if (isset($rowData[$attributeCode]) && strlen($rowData[$attributeCode])) {
                            $this->isAttributeValid($attributeCode, $attributeParams, $rowData, $rowNumber);
                        } elseif ($attributeParams['is_required']) {
                            $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNumber, $attributeCode);
                        }
                    }
                }
            }
        }
    }

    /**
     * Validate data row for delete behaviour
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return null
     */
    protected function _validateRowForDelete(array $rowData, $rowNumber)
    {
        if ($this->_checkUniqueKey($rowData, $rowNumber)) {
            $email   = strtolower($rowData[self::COLUMN_EMAIL]);
            $website = $rowData[self::COLUMN_WEBSITE];

            if (!$this->_getCustomerId($email, $website)) {
                $this->addRowError(self::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
            }
        }
    }

    /**
     * Retrieve entity attribute EAV collection
     *
     * @return Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection
     */
    protected function _getAttributeCollection()
    {
        return Mage::getResourceModel('Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection');
    }
}
