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
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->run("
INSERT INTO `{$installer->getTable('directory/country_region')}` (`country_id`, `code`, `default_name`)
VALUES
('RO', 'AB', 'Alba'), ('RO', 'AR', 'Arad'), ('RO', 'AG', 'Argeş'),
('RO', 'BC', 'Bacău'), ('RO', 'BH', 'Bihor'), ('RO', 'BN', 'Bistriţa-Năsăud'),
('RO', 'BT', 'Botoşani'), ('RO', 'BV', 'Braşov'), ('RO', 'BR', 'Brăila'),
('RO', 'B', 'Bucureşti'), ('RO', 'BZ', 'Buzău'), ('RO', 'CS', 'Caraş-Severin'),
('RO', 'CL', 'Călăraşi'), ('RO', 'CJ', 'Cluj'), ('RO', 'CT', 'Constanţa'),
('RO', 'CV', 'Covasna'), ('RO', 'DB', 'Dâmboviţa'), ('RO', 'DJ', 'Dolj'),
('RO', 'GL', 'Galaţi'), ('RO', 'GR', 'Giurgiu'), ('RO', 'GJ', 'Gorj'),
('RO', 'HR', 'Harghita'), ('RO', 'HD', 'Hunedoara'), ('RO', 'IL', 'Ialomiţa'),
('RO', 'IS', 'Iaşi'), ('RO', 'IF', 'Ilfov'), ('RO', 'MM', 'Maramureş'),
('RO', 'MH', 'Mehedinţi'), ('RO', 'MS', 'Mureş'), ('RO', 'NT', 'Neamţ'),
('RO', 'OT', 'Olt'), ('RO', 'PH', 'Prahova'), ('RO', 'SM', 'Satu-Mare'),
('RO', 'SJ', 'Sălaj'), ('RO', 'SB', 'Sibiu'), ('RO', 'SV', 'Suceava'),
('RO', 'TR', 'Teleorman'), ('RO', 'TM', 'Timiş'), ('RO', 'TL', 'Tulcea'),
('RO', 'VS', 'Vaslui'), ('RO', 'VL', 'Vâlcea'), ('RO', 'VN', 'Vrancea');
");
