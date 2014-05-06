<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Handler\AdminUserRole; 

use Magento\User\Test\Handler\AdminUserRole\AdminUserRoleInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 *
 * @package Magento\User\Test\Handler\AdminUserRole
 */
class Ui extends AbstractUi implements AdminUserRoleInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
