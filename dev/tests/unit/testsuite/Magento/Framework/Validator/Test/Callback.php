<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Validator\Test;

/**
 * Class with callback for testing callbacks
 */
class Callback
{
    const ID = 3;

    /**
     * @return int
     */
    public function getId()
    {
        return self::ID;
    }

    /**
     * Fake method for testing callbacks
     */
    public function configureValidator()
    {
    }
}
