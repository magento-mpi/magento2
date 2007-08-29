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
alter table `core_store` add column `sort_order` smallint (5)UNSIGNED   NOT NULL;
alter table `core_store` add column `is_active` tinyint (1)UNSIGNED   NOT NULL ;

alter table `core_website` add column `sort_order` smallint (5)UNSIGNED   NOT NULL ;
alter table `core_website` add column `is_active` tinyint (1)UNSIGNED   NOT NULL;

update `core_store` set `is_active`=1;
update `core_website` set `is_active`=1;

alter table `core_website` add unique `code` (`code`);
alter table `core_website` add index `is_active` (`is_active`, `sort_order`);

alter table `core_store` add index `is_active` (`is_active`, `sort_order`);