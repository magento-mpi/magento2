<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Magento\SalesArchive\Model\Resource\Synchronizer;

require_once __DIR__ . '/../../Model/Resource/Synchronizer.php';

/* @var $this \Magento\Setup\Module\Setup */
$synchronizer = new Synchronizer($this);
$synchronizer->syncArchiveStructure();
