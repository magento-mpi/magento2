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
update `core_config_data` set `value` = '0', `inherit` = '1' where `path` = 'carriers/dhl/active';
update `core_config_data` set `inherit` = '0' where `scope` = 'default' and `path` = 'carriers/dhl/active';

update `core_config_field` set `show_in_default` = '0', `show_in_website` = '0', `show_in_store` = '0' where `path` = 'carriers/dhl';

update `core_config_data` set `value` = '0', `inherit` = '1' where `path` = 'carriers/pickup/active';
update `core_config_data` set `inherit` = '0' where `scope` = 'default' and `path` = 'carriers/pickup/active';

update `core_config_field` set `show_in_default` = '0', `show_in_website` = '0', `show_in_store` = '0' where `path` = 'carriers/pickup';
