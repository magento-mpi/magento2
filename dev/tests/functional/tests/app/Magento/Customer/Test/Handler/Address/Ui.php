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
use Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 *
 * @package Magento\Customer\Test\Handler\Address
 */
class Ui extends AbstractUi implements AddressInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
