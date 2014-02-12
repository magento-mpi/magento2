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
class CircularThree
{
    /**
     * @param CircularOne $one
     */
    public function __construct(CircularOne $one)
    {
    }
} 
