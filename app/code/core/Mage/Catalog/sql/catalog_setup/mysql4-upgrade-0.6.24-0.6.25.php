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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$this->addAttribute('catalog_product', 'visibility', array(
                        'type'      => 'int',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Visibility',
                        'input'     => 'select',
                        'class'     => '',
                        'source'    => 'catalog/entity_product_attribute_source_visibility',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '3',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
));

$conn->query("
DROP TABLE IF EXISTS `catalog_product_visibility`
");
$conn->query("
CREATE TABLE `catalog_product_visibility` (
  `visibility_id` tinyint(3) unsigned NOT NULL auto_increment,
  `visibility_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`visibility_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available product visibility'
");
$conn->query("
insert into `catalog_product_visibility` (`visibility_id`,`visibility_code`) values (1,'Not Visible'),(2,'Visible In Catalog'),(3,'Visible In Search'),(4,'Visible In Both')
");

$statusAttributeId = $conn->fetchOne("SELECT attribute_id FROM eav_attribute WHERE attribute_code='status'");
$visibilityAttributeId = $conn->fetchOne("SELECT attribute_id FROM eav_attribute WHERE attribute_code='visibility'");
$conn->query("
REPLACE INTO catalog_product_entity_int (entity_type_id, attribute_id, store_id, entity_id, value)
SELECT
	entity_type_id,
	$visibilityAttributeId,
	store_id,
	entity_id,
	4
FROM
	catalog_product_entity_int
WHERE
	attribute_id=$statusAttributeId
");

