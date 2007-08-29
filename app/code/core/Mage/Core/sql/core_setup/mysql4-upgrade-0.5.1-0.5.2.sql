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
/**
 * prepare for design packages
 */

replace into core_config_data (scope, scope_id, path, `value`) values
('default', 0, 'system/filesystem/design', '{{app_dir}}/design')
,('default', 0, 'system/filesystem/skin', '{{base_dir}}/skin')
,('default', 0, 'design/package/name', 'default')
,('default', 0, 'design/package/area', 'frontend')
,('default', 0, 'design/package/theme', 'default')
,('default', 0, 'web/url/skin', '{{base_path}}skin/')
;
