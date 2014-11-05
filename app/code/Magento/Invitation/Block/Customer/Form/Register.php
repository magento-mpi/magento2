<?php
/**
 * {license_notice}
 *
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
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Registry $coreRegistry,
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
            $customerUrl,
            $data
        );
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve form data
     *
     * @return \Magento\Framework\Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $customerFormData = $this->_customerSession->getCustomerFormData(true);
            $data = new \Magento\Framework\Object($customerFormData ?: array());
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
