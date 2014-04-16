<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Handler\CustomerGroup; 

use Magento\Customer\Test\Handler\CustomerGroup\CustomerGroupInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 *
 * @package Magento\Customer\Test\Handler\CustomerGroup
 */
class Ui extends AbstractUi implements CustomerGroupInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
