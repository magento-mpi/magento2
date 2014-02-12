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
class CircularTwo
{
    /**
     * @param CircularThree $three
     */
    public function __construct(CircularThree $three)
    {
    }
} 
