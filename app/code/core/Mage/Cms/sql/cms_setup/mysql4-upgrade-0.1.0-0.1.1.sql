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
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

RENAME TABLE page_static TO cms_page;

ALTER TABLE cms_page COMMENT = 'CMS pages';

ALTER TABLE cms_page CHANGE page_creation_time page_creation_time DATETIME NULL DEFAULT NULL ,
CHANGE page_update_time page_update_time DATETIME NULL DEFAULT NULL;

ALTER TABLE cms_page CHANGE page_active page_active TINYINT( 1 ) NOT NULL;

ALTER TABLE cms_page ADD page_content TEXT NULL AFTER page_identifier;

ALTER TABLE cms_page ADD page_store_id TINYINT NOT NULL ;

ALTER TABLE cms_page ADD page_order TINYINT NOT NULL ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
