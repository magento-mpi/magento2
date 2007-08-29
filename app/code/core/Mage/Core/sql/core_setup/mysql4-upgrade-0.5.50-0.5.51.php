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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$this->setConfigData('web/cookie/cookie_domain', '');
$this->setConfigData('web/cookie/cookie_path', '');

$this->addConfigField('system/smtp', 'SMTP settings (Windows server only)');
$this->addConfigField('system/smtp/host', 'Host');
$this->addConfigField('system/smtp/port', 'Port (25)');

$this->setConfigData('system/smtp/host', 'localhost');
$this->setConfigData('system/smtp/port', '25');