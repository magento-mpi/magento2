<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

use Magento\SalesArchive\Model\Resource\Synchronizer;

/* @var $this Magento\Setup\Module\SetupModule */
$synchronizer = new Synchronizer($this);
$synchronizer->syncArchiveStructure();
