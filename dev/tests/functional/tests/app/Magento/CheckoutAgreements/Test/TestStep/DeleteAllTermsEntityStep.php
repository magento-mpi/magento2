<?php

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CheckoutAgreements\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementIndex;
use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementNew;

/**
 * Class DeleteAllTermsEntityStep
 * Delete all terms on backend
 */
class DeleteAllTermsEntityStep implements TestStepInterface
{
    /**
     * Checkout agreement index page
     *
     * @var CheckoutAgreementIndex
     */
    protected $agreementIndex;

    /**
     * Checkout agreement new and edit page
     *
     * @var CheckoutAgreementNew
     */
    protected $agreementNew;

    /**
     * @construct
     * @param CheckoutAgreementNew $agreementNew
     * @param CheckoutAgreementIndex $agreementIndex
     */
    public function __construct(
        CheckoutAgreementNew $agreementNew,
        CheckoutAgreementIndex $agreementIndex
    ) {
        $this->agreementNew = $agreementNew;
        $this->agreementIndex = $agreementIndex;
    }

    /**
     * Delete terms on backend
     *
     * @return array
     */
    public function run()
    {
        $this->agreementIndex->open();
        while ($this->agreementIndex->getAgreementGridBlock()->isFirstRowVisible()) {
            $this->agreementIndex->getAgreementGridBlock()->openFirstRow();
            $this->agreementNew->getPageActionsBlock()->delete();
        }
    }
}
