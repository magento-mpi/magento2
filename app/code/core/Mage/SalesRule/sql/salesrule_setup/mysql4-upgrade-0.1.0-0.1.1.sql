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
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

alter table `salesrule` add column `is_advanced` tinyint UNSIGNED DEFAULT 1  NOT NULL;
alter table `salesrule` add column `sort_order` int UNSIGNED   NOT NULL;
alter table `salesrule` drop key `to_date`, add index `sort_order` (`is_active`, `sort_order`, `to_date`, `from_date`);

alter table `salesrule_product` add column `sort_order` int UNSIGNED   NOT NULL;
alter table `salesrule_product` drop key `from_date`, add unique `sort_order` (`from_date`, `to_date`, `store_id`, `customer_group_id`, `product_id`, `sort_order`);
alter table `salesrule_product` change `from_date` `from_time` int UNSIGNED   NOT NULL ;
alter table `salesrule_product` change `to_date` `to_time` int UNSIGNED   NOT NULL ;
