<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$itemsPerPage = 1000;
$currentPosition = 0;

$sessionTable = $installer->getTable('core_session');

// update session data by pages
do {
    $select = $installer->getConnection()
        ->select()
        ->from($sessionTable, array('session_id', 'session_data'))
        ->limit($itemsPerPage, $currentPosition);
    $sessions = $select->query()->fetchAll();
    $currentPosition += $itemsPerPage;

    $needContinue = count($sessions) > 0;
    foreach ($sessions as $key => $session) {
        // if session data is not a base64 encoded string
        if (!base64_decode($session['session_data'], true)) {
            $sessions[$key]['session_data'] = base64_encode($session['session_data']);
        } else {
            unset($sessions[$key]);
        }
    }

    if ($sessions) {
        $installer->getConnection()->insertOnDuplicate($sessionTable, $sessions, array('session_data'));
    }
} while ($needContinue);

$installer->endSetup();
