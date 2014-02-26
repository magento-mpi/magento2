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
 */
namespace Magento\Invitation\Block\Customer\Form;

class Register extends \Magento\Customer\Block\Form\Register
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Registry $coreRegistry
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $context,
            $coreData,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $moduleManager,
            $customerSession,
            $customerHelper,
            $data
        );
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve form data
     *
     * @return \Magento\Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $customerFormData = $this->_customerSession->getCustomerFormData(true);
            $data = new \Magento\Object($customerFormData ?: array());
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
     * @return \Magento\Invitation\Model\Invitation
     */
    public function getCustomerInvitation()
    {
        return $this->_coreRegistry->registry('current_invitation');
    }
}
