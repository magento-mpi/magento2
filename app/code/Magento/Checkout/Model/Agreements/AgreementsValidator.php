<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Model\Agreements;

/**
 * Class AgreementsValidator
 */
class AgreementsValidator
{
    /** @var  AgreementsProviderInterface[] */
    protected $agreementsProviders;

    /**
     * @param AgreementsProviderInterface[] $list
     */
    public function  __construct($list)
    {
        $this->agreementsProviders = (array) $list;
    }

    /**
     * Validate that all required agreements is signed
     *
     * @param int[] $agreementIds
     * @return boll
     */
    public function isValid($agreementIds)
    {
        $requiredAgreements = [];
        foreach ($this->agreementsProviders as $agreementsProvider) {
            $requiredAgreements = array_merge($requiredAgreements, $agreementsProvider->getRequiredAgreementIds());
        }
        $agreementsDiff = array_diff($requiredAgreements, $agreementIds);
        return empty($agreementsDiff);
    }
}