<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Handler\Customer; 

use Magento\Customer\Test\Handler\Customer\CustomerInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 *
 * @package Magento\Customer\Test\Handler\Customer
 */
class Curl extends AbstractCurl implements CustomerInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
