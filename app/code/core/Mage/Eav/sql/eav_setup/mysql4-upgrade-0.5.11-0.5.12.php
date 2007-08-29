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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$conn->dropForeignKey('eav_entity_attribute', 'FK_EAV_ENTITY_ATTRIVUTE_ATTRIBUTE');
$conn->dropForeignKey('eav_entity_attribute', 'FK_EAV_ENTITY_ATTRIVUTE_GROUP');

$conn->multi_query(<<<EOT
delete from eav_attribute_set where entity_type_id NOT IN (select entity_type_id from eav_entity_type);
delete from eav_entity_attribute where attribute_set_id NOT IN (select attribute_set_id from eav_attribute_set);

alter table `eav_entity_attribute` 
    ,add constraint `FK_EAV_ENTITY_ATTRIVUTE_ATTRIBUTE` foreign key(`attribute_id`) references `eav_attribute` (`attribute_id`) on delete cascade  on update cascade
    ,add constraint `FK_EAV_ENTITY_ATTRIVUTE_GROUP` foreign key(`attribute_group_id`) references `eav_attribute_group` (`attribute_group_id`) on delete cascade  on update cascade;

EOT
);