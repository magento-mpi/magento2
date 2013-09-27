<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer registration form block
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Block_Customer_Form_Register extends Magento_Customer_Block_Form_Register
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Customer Session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_session;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory
     * @param Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory
     * @param Magento_Customer_Model_Session $session
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory,
        Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory,
        Magento_Customer_Model_Session $session,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $configCacheType,
            $coreData,
            $context,
            $storeManager,
            $regionCollFactory,
            $countryCollFactory,
            $data
        );
        $this->_session = $session;
    }

    /**
     * Retrieve form data
     *
     * @return Magento_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $customerFormData = $this->_session->getCustomerFormData(true);
            $data = new Magento_Object($customerFormData);
            if (empty($customerFormData)) {
                $invitation = $this->getCustomerInvitation();

                if ($invitation->getId()) {
                    // check, set invitation email
                    $data->setEmail($invitation->getEmail());
                }
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }


    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/createpost', array('_current' => true));
    }

    /**
     * Retrieve customer invitation
     *
     * @return Magento_Invitation_Model_Invitation
     */
    public function getCustomerInvitation()
    {
        return $this->_coreRegistry->registry('current_invitation');
    }
}
