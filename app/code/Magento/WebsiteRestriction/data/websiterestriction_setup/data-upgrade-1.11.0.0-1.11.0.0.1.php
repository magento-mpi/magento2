<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright  {copyright}
 * @license    {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();

// will not change template if already set
$connection->update(
    $installer->getTable('cms_page'),
    array('root_template' => 'one_column'),
    array('identifier IN (?)' => array('service-unavailable','private-sales'), 'root_template IS NULL')
);
