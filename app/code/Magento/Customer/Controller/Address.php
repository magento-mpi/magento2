<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer address controller
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Address extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var \Magento\Customer\Model\Address\FormFactory
     */
    protected $_addressFormFactory;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerData;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;


    /** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface */
    protected $_addressService;

    /**
     * @var FormFactory
     */
    protected $_formFactory;

    /**
     * @var \Magento\Customer\Service\V1\Dto\RegionBuilder
     */
    protected $_regionBuilder;

    /**
     * @var \Magento\Customer\Service\V1\Dto\AddressBuilder
     */
    protected $_addressBuilder;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\Address\FormFactory $addressFormFactory
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     * @param \Magento\Customer\Service\V1\Dto\RegionBuilder $regionBuilder
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\Address\FormFactory $addressFormFactory,
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Customer\Service\V1\Dto\RegionBuilder $regionBuilder,
        \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder
    ) {
        $this->_customerSession = $customerSession;
        $this->_addressFactory = $addressFactory;
        $this->_addressFormFactory = $addressFormFactory;
        $this->_customerData = $customerData;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_addressService = $addressService;
        $this->_formFactory = $formFactory;
        $this->_regionBuilder = $regionBuilder;
        $this->_addressBuilder = $addressBuilder;
        parent::__construct($context);
    }

    /**
     * Retrieve customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_customerSession;
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate($this)) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Customer addresses list
     */
    public function indexAction()
    {
        $addresses = $this->_addressService->getAddresses($this->_getSession()->getCustomerId());
        if (count($addresses)) {
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();

            $block = $this->_view->getLayout()->getBlock('address_book');
            if ($block) {
                $block->setRefererUrl($this->_redirect->getRefererUrl());
            }
            $this->_view->renderLayout();
        } else {
            $this->getResponse()->setRedirect($this->_buildUrl('*/*/new'));
        }
    }

    public function editAction()
    {
        $this->_forward('form');
    }

    public function newAction()
    {
        $this->_forward('form');
    }

    /**
     * Address book form
     */
    public function formAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $navigationBlock = $this->_view->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('customer/address');
        }
        $this->_view->renderLayout();
    }

    /**
     * Process address form save
     */
    public function formPostAction()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('*/*/');
        }

        if (!$this->getRequest()->isPost()) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
            $this->getResponse()->setRedirect($this->_redirect->error($this->_buildUrl('*/*/edit')));
            return;
        }
        $customerId = $this->_getSession()->getCustomerId();
        try {
            $address = $this->_extractAddress();
            $this->_addressService->saveAddresses($customerId, [$address]);
            $this->messageManager->addSuccess(__('The address has been saved.'));
            $url = $this->_buildUrl('*/*/index', array('_secure'=>true));
            $this->getResponse()->setRedirect($this->_redirect->success($url));
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addException($e, $e->getMessage());
        } catch (\Magento\Validator\ValidatorException $e) {
            foreach ($e->getMessages() as $messages) {
                foreach ($messages as $message) {
                    $this->messageManager->addError($message);
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Cannot save address.'));
        }

        $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
        $url = $this->_buildUrl('*/*/edit', array('id' => $address->getId()));
        $this->getResponse()->setRedirect($this->_redirect->error($url));
    }

    /**
     * Do address validation using validate methods in models
     *
     * @param \Magento\Customer\Model\Address $address
     * @throws \Magento\Validator\ValidatorException
     */
    protected function _validateAddress($address)
    {
        $addressErrors = $address->validate();
        if (is_array($addressErrors) && count($addressErrors) > 0) {
            throw new \Magento\Validator\ValidatorException(array($addressErrors));
        }
    }

    /**
     * Extract address from request
     *
     * @return \Magento\Customer\Model\Address
     */
    protected function _extractAddress()
    {
        $customerId = $this->_getSession()->getCustomerId();
        /* @var \Magento\Customer\Model\Address $address */
        $address  = $this->_createAddress();
        $addressId = $this->getRequest()->getParam('id');
        $existingAddressData = [];
        if ($addressId) {
            $existingAddress = $this->_addressService->getAddressById($customerId, $addressId);
            if ($existingAddress->getId()) {
                $existingAddressData = $existingAddress->__toArray();
            }
        }

        /** @var \Magento\Customer\Model\Metadata\Form $addressForm */
        $addressForm = $this->_formFactory->create(
            'customer_address',
            'customer_address_edit',
            $address->getAttributes()
        );
        $addressData = $addressForm->extractData($this->getRequest());
        $attributeValues = $addressForm->compactData($addressData);
        $region = $this->_regionBuilder->setRegionCode('')
            ->setRegion($attributeValues['region'])
            ->setRegionId($attributeValues['region_id'])
            ->create();
        unset($attributeValues['region'], $attributeValues['region_id']);

        $this->_addressBuilder->populateWithArray(array_merge($existingAddressData, $attributeValues));
        $address
            ->setRegion($region)
            ->setDefaultBilling($this->getRequest()->getParam('default_billing', false))
            ->setDefaultShipping($this->getRequest()->getParam('default_shipping', false));
        return $address;
    }

    public function deleteAction()
    {
        $addressId = $this->getRequest()->getParam('id', false);

        if ($addressId) {
            $address = $this->_createAddress();
            $address->load($addressId);

            // Validate address_id <=> customer_id
            if ($address->getCustomerId() != $this->_getSession()->getCustomerId()) {
                $this->messageManager->addError(__('The address does not belong to this customer.'));
                $this->getResponse()->setRedirect($this->_buildUrl('*/*/index'));
                return;
            }

            try {
                $address->delete();
                $this->messageManager->addSuccess(__('The address has been deleted.'));
            } catch (\Exception $e){
                $this->messageManager->addException($e, __('An error occurred while deleting the address.'));
            }
        }
        $this->getResponse()->setRedirect($this->_buildUrl('*/*/index'));
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function _buildUrl($route = '', $params = array())
    {
        /** @var \Magento\Core\Model\Url $urlBuilder */
        $urlBuilder = $this->_objectManager->create('Magento\Core\Model\Url');
        return $urlBuilder->getUrl($route, $params);
    }

    /**
     * @return \Magento\Customer\Model\Address
     */
    protected function _createAddress()
    {
        return $this->_addressFactory->create();
    }

    /**
     * @return \Magento\Customer\Model\Address\Form
     */
    protected function _createAddressForm()
    {
        return $this->_addressFormFactory->create();
    }
}
