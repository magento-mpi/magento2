<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model;

interface PaymentTypeInterface
{
    /**
     * @var string
     */
    const REGULAR = 'regular';

    /**
     * @var string
     */
    const TRIAL = 'trial';

    /**
     * @var string
     */
    const INITIAL = 'initial';
}
