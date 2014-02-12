<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\ObjectManager\Factory\Fixture;

/**
 * Part of the chain for circular dependency test
 */
class CircularOne
{
    /**
     * @param CircularTwo $two
     */
    public function __construct(CircularTwo $two)
    {
    }
} 
