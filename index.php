<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$bootstrapFile = 'app/bootstrap.php';

if (!file_exists($bootstrapFile)) {
    if (is_dir('downloader')) {
        header("Location: downloader");
    } else {
        echo "{$bootstrapFile} was not found";
    }
    exit;
}

if (file_exists('maintenance.flag')) {
    include_once dirname(__FILE__) . '/pub/errors/503.php';
    exit;
}

require_once $bootstrapFile;

#Magento_Profiler::enable();
#Magento_Profiler::registerOutput(new Magento_Profiler_Output_Html());
#Magento_Profiler::registerOutput(new Magento_Profiler_Output_Firebug());
#Magento_Profiler::registerOutput(new Magento_Profiler_Output_Csvfile(__DIR__ . '/var/log/profiler.csv'));

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';
/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

Mage::run($mageRunCode, $mageRunType);
