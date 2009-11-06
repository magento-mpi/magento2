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
INSERT INTO `{$installer->getTable('directory/country_region_name')}` (`locale`, `region_id`, `name`)
VALUES
('en_US', 278, 'Alba'), ('en_US', 279, 'Arad'), ('en_US', 280, 'Argeş'),
('en_US', 281, 'Bacău'), ('en_US', 282, 'Bihor'), ('en_US', 283, 'Bistriţa-Năsăud'),
('en_US', 284, 'Botoşani'), ('en_US', 285, 'Braşov'), ('en_US', 286, 'Brăila'),
('en_US', 287, 'Bucureşti'), ('en_US', 288, 'Buzău'), ('en_US', 289, 'Caraş-Severin'),
('en_US', 290, 'Călăraşi'), ('en_US', 291, 'Cluj'), ('en_US', 292, 'Constanţa'),
('en_US', 293, 'Covasna'), ('en_US', 294, 'Dâmboviţa'), ('en_US', 295, 'Dolj'),
('en_US', 296, 'Galaţi'), ('en_US', 297, 'Giurgiu'), ('en_US', 298, 'Gorj'),
('en_US', 299, 'Harghita'), ('en_US', 300, 'Hunedoara'), ('en_US', 301, 'Ialomiţa'),
('en_US', 302, 'Iaşi'), ('en_US', 303, 'Ilfov'), ('en_US', 304, 'Maramureş'),
('en_US', 305, 'Mehedinţi'), ('en_US', 306, 'Mureş'), ('en_US', 307, 'Neamţ'),
('en_US', 308, 'Olt'), ('en_US', 309, 'Prahova'), ('en_US', 310, 'Satu-Mare'),
('en_US', 311, 'Sălaj'), ('en_US', 312, 'Sibiu'), ('en_US', 313, 'Suceava'),
('en_US', 314, 'Teleorman'), ('en_US', 315, 'Timiş'), ('en_US', 316, 'Tulcea'),
('en_US', 317, 'Vaslui'), ('en_US', 318, 'Vâlcea'), ('en_US', 319, 'Vrancea');
");
