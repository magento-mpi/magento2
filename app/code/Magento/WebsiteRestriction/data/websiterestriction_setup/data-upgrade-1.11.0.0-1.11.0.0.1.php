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
    ['page_layout' => '1column'],
    ['identifier IN (?)' => ['service-unavailable', 'private-sales'], 'page_layout IS NULL']
);
