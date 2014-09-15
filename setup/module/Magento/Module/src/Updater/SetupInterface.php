<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Updater;

interface SetupInterface
{
    const DEFAULT_SETUP_CONNECTION = 'setup_setup';

    const VERSION_COMPARE_EQUAL = 0;

    const VERSION_COMPARE_LOWER = -1;

    const VERSION_COMPARE_GREATER = 1;

    const TYPE_DB_INSTALL = 'install';

    const TYPE_DB_UPGRADE = 'upgrade';

    const TYPE_DB_RECURRING = 'recurring';

    const TYPE_DATA_INSTALL = 'data-install';

    const TYPE_DATA_UPGRADE = 'data-upgrade';

    /**
     * Check call afterApplyAllUpdates method for setup class
     *
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getCallAfterApplyAllUpdates();

    /**
     * Run each time after applying of all updates,
     *
     * @return \Magento\Module\Updater\SetupInterface
     */
    public function afterApplyAllUpdates();

    /**
     *  Apply data updates to the system after upgrading
     *
     * @return void
     */
    public function applyDataUpdates();
}
