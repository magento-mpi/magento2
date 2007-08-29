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
alter table `core_config_field` add unique `IDX_PATH` ( `path` );

/**
 * prepare store root_category
 */

replace into core_config_field (`path`, `frontend_label`, `frontend_type`, `source_model`) values
('catalog', 'Catalog', 'text', ''),
('catalog/category', 'Category', 'text', ''),
('catalog/category/root_id', 'Root category', 'select', 'adminhtml/system_config_source_category');
