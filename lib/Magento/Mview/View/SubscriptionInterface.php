<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Mview\View;

interface SubscriptionInterface
{
    /**
     * @return \Magento\Mview\View\SubscriptionInterface
     */
    public function create();

    /**
     * @return mixed
     */
    public function remove();
}
