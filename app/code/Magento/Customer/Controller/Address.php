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

use Magento\Customer\Service\Entity\V1\Exception;
use Magento\App\RequestInterface;

class Address extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;


    /** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface */
    protected $_addressService;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
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
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     * @param \Magento\Customer\Service\V1\Dto\RegionBuilder $regionBuilder
     * @param \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder
     * @internal param \Magento\Customer\Helper\Data $customerData
     * @internal param \Magento\Customer\Model\AddressFactory $addressFactory
     * @internal param \Magento\Customer\Model\Address\FormFactory $addressFormFactory
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Customer\Service\V1\Dto\RegionBuilder $regionBuilder,
        \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder
    ) {
        $this->_customerSession = $customerSession;
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
     * @return \Magento\Customer\Service\V1\Dto\Address
     */
    protected function _extractAddress()
    {
        $customerId = $this->_getSession()->getCustomerId();

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
            $existingAddressData
        );
        $addressData = $addressForm->extractData($this->getRequest());
        $attributeValues = $addressForm->compactData($addressData);
        $region = $this->_regionBuilder->setRegionCode('')
            ->setRegion($attributeValues['region'])
            ->setRegionId($attributeValues['region_id'])
            ->create();
        unset($attributeValues['region'], $attributeValues['region_id']);

        return $this->_addressBuilder->populateWithArray(array_merge($existingAddressData, $attributeValues))
            ->setRegion($region)
            ->setDefaultBilling($this->getRequest()->getParam('default_billing', false))
            ->setDefaultShipping($this->getRequest()->getParam('default_shipping', false))
            ->create();
    }

    public function deleteAction()
    {
        $addressId = $this->getRequest()->getParam('id', false);

        if ($addressId) {
            try {
                $address = $this->_addressService->getAddressById($addressId);
                if ($address->getCustomerId() === $this->_getSession()->getCustomerId()) {
                    $this->_addressService->deleteAddress($addressId);
                    $this->messageManager->addSuccess(__('The address has been deleted.'));
                } else {
                    $this->messageManager->addError(__('An error occurred while deleting the address.'));
                }
            } catch (\Exception $other) {
                $this->messageManager->addException($other, __('An error occurred while deleting the address.'));
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
}
