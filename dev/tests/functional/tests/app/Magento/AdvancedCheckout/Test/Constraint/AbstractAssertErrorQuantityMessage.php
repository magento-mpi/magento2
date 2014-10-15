<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;

/**
 * Class AbstractAssertErrorQuantityMessage
 * Abstract class for implementing asserts with error quantity message
 */
abstract class AbstractAssertErrorQuantityMessage extends AbstractConstraint
{
    /**
     * Error requested quantity message
     */
    const ERROR_QUANTITY_MESSAGE = 'The product cannot be added to cart in requested quantity.';
}
