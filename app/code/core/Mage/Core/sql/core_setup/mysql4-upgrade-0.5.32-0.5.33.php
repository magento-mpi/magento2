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


$conn->delete($this->getTable('core/config_field'), "path like 'advanced/datashare/%'");
$conn->delete($this->getTable('core/config_data'), "path like 'advanced/datashare/%'");

$this->addConfigField('advanced/datashare', 'Datasharing', array(
	'show_in_store'=>0,
));

$this->addConfigField('advanced/datashare/default', 'Default', array(
	'frontend_type'=>'multiselect',
	'backend_model'=>'adminhtml/system_config_backend_datashare',
	'source_model'=>'adminhtml/system_config_source_store',
));

#Mage::getSingleton('core/store')->updateDatasharing();

$this->addConfigField('dev/mode', 'Operating mode');
$this->addConfigField('dev/mode/checksum', 'Validate config checksums', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
));

$this->setConfigData('dev/mode/checksum', 1);