<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Model\Agreements;

/**
 * Interface AgreementsProviderInterface
 */
interface AgreementsProviderInterface
{
    /**
     * Get list of Required Agreement Ids
     *
     * @return int[]
     */
    public function getRequiredAgreementIds();
} 