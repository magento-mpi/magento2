<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Mview;

interface ViewInterface
{
    /**
     * @return \Magento\Mview\View\SubscriptionInterface
     */
    public function subscribe();

    /**
     * @return mixed
     */
    public function unsubscribe();

    /**
     * @return mixed
     */
    public function update();

    /**
     * @return mixed
     */
    public function getChangelog();
}
