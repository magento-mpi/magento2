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
 * Import customer finance entity model
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method      array getData() getData()
 */
namespace Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer;

class Finance
    extends \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract
{
    /**
     * Attribute collection name
     */
    const ATTRIBUTE_COLLECTION_NAME = 'Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection';

    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL           = '_email';
    const COLUMN_WEBSITE         = '_website';
    const COLUMN_FINANCE_WEBSITE = '_finance_website';
    /**#@-*/

    /**#@+
     * Error codes
     */
    const ERROR_FINANCE_WEBSITE_IS_EMPTY = 'financeWebsiteIsEmpty';
    const ERROR_INVALID_FINANCE_WEBSITE  = 'invalidFinanceWebsite';
    const ERROR_DUPLICATE_PK             = 'duplicateEmailSiteFinanceSite';
    /**#@-*/

    /**
     * Permanent entity columns
     *
     * @var array
     */
    protected $_permanentAttributes = array(self::COLUMN_WEBSITE, self::COLUMN_EMAIL, self::COLUMN_FINANCE_WEBSITE);

    /**
     * Column names that holds values with particular meaning
     *
     * @var array
     */
    protected $_specialAttributes = array(
        self::COLUMN_ACTION,
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
     * Address attributes collection
     *
     * @var \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection
     */
    protected $_attributeCollection;

    /**
     * Helper to check whether modules are enabled/disabled
     *
     * @var \Magento\ScheduledImportExport\Helper\Data
     */
    protected $_importExportData;

    /**
     * Admin user object
     *
     * @var \Magento\User\Model\User
     */
    protected $_adminUser;

    /**
     * Store imported row primary keys
     *
     * @var array
     */
    protected $_importedRowPks = array();

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\CustomerBalance\Model\BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_ScheduledImportExport_Helper_Data $importExportData
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_String $coreString,
        Magento_ScheduledImportExport_Helper_Data $importExportData,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        array $data = array()
    ) {
        // entity type id has no meaning for finance import
        $data['entity_type_id'] = -1;

        parent::__construct($coreData, $coreString, $data);

        $this->_rewardFactory = $rewardFactory;
        $this->_customerFactory = $customerFactory;
        $this->_balanceFactory = $balanceFactory;
        $this->_importExportData = $importExportData;

        $this->_adminUser = isset($data['admin_user']) ? $data['admin_user']
            : \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser();

        $this->addMessageTemplate(self::ERROR_FINANCE_WEBSITE_IS_EMPTY,
            __('Finance information website is not specified')
        );
        $this->addMessageTemplate(self::ERROR_INVALID_FINANCE_WEBSITE,
            __('Invalid value in Finance information website column')
        );
        $this->addMessageTemplate(self::ERROR_DUPLICATE_PK,
            __('Row with such email, website, finance website combination was already found.')
        );
        $this->_initAttributes();
    }

    /**
     * Initialize entity attributes
     *
     * @return \Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance
     */
    protected function _initAttributes()
    {
        /** @var $attribute \Magento\Eav\Model\Attribute */
        foreach ($this->_attributeCollection as $attribute) {
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
        if (!$this->_importExportData->isRewardPointsEnabled()
            && !$this->_importExportData->isCustomerBalanceEnabled()) {
            return false;
        }

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $this->_customerFactory->create();
        $rewardPointsKey =
            \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection::COLUMN_REWARD_POINTS;
        $customerBalanceKey =
            \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection::COLUMN_CUSTOMER_BALANCE;

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

                $websiteId = $this->_websiteCodeToId[$rowData[self::COLUMN_FINANCE_WEBSITE]];
                // save finance data for customer
                foreach ($this->_attributes as $attributeCode => $attributeParams) {
                    if ($this->getBehavior($rowData) == \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE) {
                        if ($attributeCode == $rewardPointsKey) {
                            $this->_deleteRewardPoints($customer, $websiteId);
                        } elseif ($attributeCode == $customerBalanceKey) {
                            $this->_deleteCustomerBalance($customer, $websiteId);
                        }
                    } elseif ($this->getBehavior($rowData) == \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE) {
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
     * Update reward points value for customerEtn
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param int $websiteId
     * @param int $value reward points value
     * @return \Magento\Reward\Model\Reward
     */
    protected function _updateRewardPointsForCustomer(\Magento\Customer\Model\Customer $customer, $websiteId, $value)
    {
        /** @var $rewardModel \Magento\Reward\Model\Reward */
        $rewardModel = $this->_rewardFactory->create();
        $rewardModel->setCustomer($customer)
            ->setWebsiteId($websiteId)
            ->loadByCustomer();

        return $this->_updateRewardValue($rewardModel, $value);
    }

    /**
     * Update reward points value for reward model
     *
     * @param \Magento\Reward\Model\Reward $rewardModel
     * @param int $value reward points value
     * @return \Magento\Reward\Model\Reward
     */
    protected function _updateRewardValue(\Magento\Reward\Model\Reward $rewardModel, $value)
    {
        $pointsDelta = $value - $rewardModel->getPointsBalance();
        if ($pointsDelta != 0) {
            $rewardModel->setPointsDelta($pointsDelta)
                ->setAction(\Magento\Reward\Model\Reward::REWARD_ACTION_ADMIN)
                ->setComment($this->_getComment())
                ->updateRewardPoints();
        }

        return $rewardModel;
    }

    /**
     * Update store credit balance for customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param int $websiteId
     * @param float $value store credit balance
     * @return \Magento\CustomerBalance\Model\Balance
     */
    protected function _updateCustomerBalanceForCustomer(\Magento\Customer\Model\Customer $customer, $websiteId, $value)
    {
        /** @var $balanceModel \Magento\CustomerBalance\Model\Balance */
        $balanceModel = $this->_balanceFactory->create();
        $balanceModel->setCustomer($customer)
            ->setWebsiteId($websiteId)
            ->loadByCustomer();

        return $this->_updateCustomerBalanceValue($balanceModel, $value);
    }

    /**
     * Update balance for customer balance model
     *
     * @param \Magento\CustomerBalance\Model\Balance $balanceModel
     * @param float $value store credit balance
     * @return \Magento\CustomerBalance\Model\Balance
     */
    protected function _updateCustomerBalanceValue(\Magento\CustomerBalance\Model\Balance $balanceModel, $value)
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
     * @param \Magento\Customer\Model\Customer $customer
     * @param int $websiteId
     */
    protected function _deleteRewardPoints(\Magento\Customer\Model\Customer $customer, $websiteId)
    {
        $this->_updateRewardPointsForCustomer($customer, $websiteId, 0);
    }

    /**
     * Delete store credit balance for customer (just set it to 0)
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param int $websiteId
     */
    protected function _deleteCustomerBalance(\Magento\Customer\Model\Customer $customer, $websiteId)
    {
        $this->_updateCustomerBalanceForCustomer($customer, $websiteId, 0);
    }

    /**
     * Retrieve comment string
     *
     * @return string
     */
    protected function _getComment()
    {
        if (!$this->_comment) {
            $this->_comment = __('Data was imported by %1',
                $this->_adminUser->getUsername()
            );
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
                $email          = strtolower($rowData[self::COLUMN_EMAIL]);
                $website        = $rowData[self::COLUMN_WEBSITE];
                $financeWebsite = $rowData[self::COLUMN_FINANCE_WEBSITE];
                $customerId     = $this->_getCustomerId($email, $website);

                if (!isset($this->_websiteCodeToId[$financeWebsite])
                    || $this->_websiteCodeToId[$financeWebsite] == \Magento\Core\Model\AppInterface::ADMIN_STORE_ID
                ) {
                    $this->addRowError(self::ERROR_INVALID_FINANCE_WEBSITE, $rowNumber, self::COLUMN_FINANCE_WEBSITE);
                } elseif ($customerId === false) {
                    $this->addRowError(self::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
                } elseif ($this->_checkRowDuplicate($customerId, $financeWebsite)) {
                    $this->addRowError(self::ERROR_DUPLICATE_PK, $rowNumber);
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
            if (empty($rowData[self::COLUMN_FINANCE_WEBSITE])) {
                $this->addRowError(self::ERROR_FINANCE_WEBSITE_IS_EMPTY, $rowNumber, self::COLUMN_FINANCE_WEBSITE);
            } else {
                $email          = strtolower($rowData[self::COLUMN_EMAIL]);
                $website        = $rowData[self::COLUMN_WEBSITE];
                $financeWebsite = $rowData[self::COLUMN_FINANCE_WEBSITE];

                if (!isset($this->_websiteCodeToId[$financeWebsite])
                    || $this->_websiteCodeToId[$financeWebsite] == \Magento\Core\Model\AppInterface::ADMIN_STORE_ID
                ) {
                    $this->addRowError(self::ERROR_INVALID_FINANCE_WEBSITE, $rowNumber, self::COLUMN_FINANCE_WEBSITE);
                } elseif (!$this->_getCustomerId($email, $website)) {
                    $this->addRowError(self::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
                }
            }
        }
    }

    /**
     * Check whether row with such email, website, finance website combination was already found in import file
     *
     * @param int $customerId
     * @param string $financeWebsite
     * @return bool
     */
    protected function _checkRowDuplicate($customerId, $financeWebsite)
    {
        $financeWebsiteId = $this->_websiteCodeToId[$financeWebsite];
        if (!isset($this->_importedRowPks[$customerId][$financeWebsiteId])) {
            $this->_importedRowPks[$customerId][$financeWebsiteId] = true;
            return false;
        } else {
            return true;
        }
    }
}
