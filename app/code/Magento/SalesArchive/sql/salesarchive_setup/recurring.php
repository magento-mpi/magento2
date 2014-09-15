<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Magento\SalesArchive\Model\Resource\Synchronizer;

/* @var $this \Magento\Setup\Module\Setup */
$synchronizer = new Synchronizer($this);
$synchronizer->syncArchiveStructure();
