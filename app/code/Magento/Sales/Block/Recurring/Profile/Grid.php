<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile view grid
 */
class Magento_Sales_Block_Recurring_Profile_Grid extends Magento_Sales_Block_Recurring_Profiles
{
    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @var Magento_Sales_Model_Recurring_Profile
     */
    protected $_recurringProfile;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Profiles collection
     *
     * @var Magento_Sales_Model_Resource_Recurring_Profile_Collection
     */
    protected $_profiles = null;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Sales_Model_Recurring_Profile $profile
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Helper_Data $coreData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Sales_Model_Recurring_Profile $profile,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Helper_Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_recurringProfile = $profile;
        $this->_registry = $registry;
        $this->_storeManager = $storeManager;
        $this->_locale = $locale;
    }

    /**
     * Instantiate profiles collection
     *
     * @param array|int|string $fields
     */
    protected function _prepareProfiles($fields = '*')
    {
        $this->_profiles = $this->_recurringProfile->getCollection()
            ->addFieldToFilter('customer_id', $this->_registry->registry('current_customer')->getId())
            ->addFieldToSelect($fields)
            ->setOrder('profile_id', 'desc');
    }

    /**
     * Prepare grid data
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_prepareProfiles(array('reference_id', 'state', 'created_at', 'updated_at', 'method_code'));

        $pager = $this->getLayout()->createBlock('Magento_Page_Block_Html_Pager')
            ->setCollection($this->_profiles)->setIsOutputRequired(false);
        $this->setChild('pager', $pager);

        $this->setGridColumns(array(
            new Magento_Object(array(
                'index' => 'reference_id',
                'title' => $this->_recurringProfile->getFieldLabel('reference_id'),
                'is_nobr' => true,
                'width' => 1,
            )),
            new Magento_Object(array(
                'index' => 'state',
                'title' => $this->_recurringProfile->getFieldLabel('state'),
            )),
            new Magento_Object(array(
                'index' => 'created_at',
                'title' => $this->_recurringProfile->getFieldLabel('created_at'),
                'is_nobr' => true,
                'width' => 1,
                'is_amount' => true,
            )),
            new Magento_Object(array(
                'index' => 'updated_at',
                'title' => $this->_recurringProfile->getFieldLabel('updated_at'),
                'is_nobr' => true,
                'width' => 1,
            )),
            new Magento_Object(array(
                'index' => 'method_code',
                'title' => $this->_recurringProfile->getFieldLabel('method_code'),
                'is_nobr' => true,
                'width' => 1,
            )),
        ));

        $profiles = array();
        $store = $this->_storeManager->getStore();
        foreach ($this->_profiles as $profile) {
            $profile->setStore($store)->setLocale($this->_locale);
            $profiles[] = new Magento_Object(array(
                'reference_id' => $profile->getReferenceId(),
                'reference_id_link_url' => $this->getUrl(
                    'sales/recurring_profile/view/',
                    array('profile' => $profile->getId())
                ),
                'state'       => $profile->renderData('state'),
                'created_at'  => $this->formatDate($profile->getData('created_at'), 'medium', true),
                'updated_at'  => $profile->getData('updated_at')
                    ? $this->formatDate($profile->getData('updated_at'), 'short', true)
                    : '',
                'method_code' => $profile->renderData('method_code'),
            ));
        }
        if ($profiles) {
            $this->setGridElements($profiles);
        }
    }
}
