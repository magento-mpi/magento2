<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Update "All resources" resource id
 */
$rule = Mage::getModel('Mage_User_Model_Rules');
$rule->load('all', 'resource_id')
    ->setResourceId('Mage_Adminhtml::all')
    ->save();
