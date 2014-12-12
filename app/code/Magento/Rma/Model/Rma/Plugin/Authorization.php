<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Model\Rma\Plugin;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Rma\Model\Rma;
use Magento\Rma\Model\RmaRepository;

class Authorization
{
    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @param UserContextInterface $userContext
     */
    public function __construct(
        UserContextInterface $userContext
    ) {
        $this->userContext = $userContext;
    }

    /**
     * Check if rma is allowed
     *
     * @param RmaRepository $subject
     * @param Rma $rmaModel
     * @return Rma
     * @throws
     */
    public function afterGet(
        RmaRepository $subject,
        Rma $rmaModel
    ) {
        if (!$this->isAllowed($rmaModel)) {
            throw NoSuchEntityException::singleField('rmaId', $rmaModel->getCustomerId());
        }
        return $rmaModel;
    }

    /**
     * Check whether rma is allowed for current user context
     *
     * @param Rma $rmaModel
     * @return bool
     */
    protected function isAllowed(Rma $rmaModel)
    {
        return $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER
            ? $rmaModel->getCustomerId() == $this->userContext->getUserId()
            : true;
    }
}
