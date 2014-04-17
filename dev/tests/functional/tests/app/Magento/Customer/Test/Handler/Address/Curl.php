<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Handler\Address; 

use Magento\Customer\Test\Handler\Address\AddressInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 *
 * @package Magento\Customer\Test\Handler\Address
 */
class Curl extends AbstractCurl implements AddressInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
