<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Model\Exception;

/**
 * Gift registry frontend controller
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \Magento\Framework\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\GiftRegistry\Helper\Data')->isEnabled()) {
            throw new NotFoundException();
        }

        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->getResponse()->setRedirect(
                $this->_objectManager->get('Magento\Customer\Model\Url')->getLoginUrl()
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Get current customer session
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_objectManager->get('Magento\Customer\Model\Session');
    }

    /**
     * Load gift registry entity model by request argument
     *
     * @param string $requestParam
     * @return \Magento\GiftRegistry\Model\Entity
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _initEntity($requestParam = 'id')
    {
        $entity = $this->_objectManager->create('Magento\GiftRegistry\Model\Entity');
        $customerId = $this->_getSession()->getCustomerId();
        $entityId = $this->getRequest()->getParam($requestParam);

        if ($entityId) {
            $entity->load($entityId);
            if (!$entity->getId() || $entity->getCustomerId() != $customerId) {
                throw new Exception(__('Please correct the gift registry ID.'));
            }
        }
        return $entity;
    }
}
