<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/** @var $installer \Magento\WebsiteRestriction\Model\Resource\Setup */
$installer = $this;
$connection = $installer->getConnection();

// will not change template if already set
$connection->update(
    $installer->getTable('cms_page'),
    array('page_layout' => '1column'),
    array('identifier IN (?)' => array('service-unavailable', 'private-sales'), 'page_layout IS NULL')
);
