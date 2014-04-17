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
use Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 *
 * @package Magento\Customer\Test\Handler\Customer
 */
class Ui extends AbstractUi implements CustomerInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
