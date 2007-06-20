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
