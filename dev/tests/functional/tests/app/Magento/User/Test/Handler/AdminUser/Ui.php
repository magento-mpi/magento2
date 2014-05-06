<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Handler\AdminUser; 

use Magento\User\Test\Handler\AdminUser\AdminUserInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 *
 * @package Magento\User\Test\Handler\AdminUser
 */
class Ui extends AbstractUi implements AdminUserInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
