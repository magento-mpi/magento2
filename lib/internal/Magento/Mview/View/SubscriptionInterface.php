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
     * Create subsciption
     *
     * @return \Magento\Mview\View\SubscriptionInterface
     */
    public function create();

    /**
     * Remove subscription
     *
     * @return \Magento\Mview\View\SubscriptionInterface
     */
    public function remove();

    /**
     * Retrieve View related to subscription
     *
     * @return \Magento\Mview\ViewInterface
     */
    public function getView();

    /**
     * Retrieve table name
     *
     * @return string
     */
    public function getTableName();

    /**
     * Retrieve table column name
     *
     * @return string
     */
    public function getColumnName();
}
