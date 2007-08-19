SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

UPDATE `eav_attribute` SET `frontend_label` = 'Page Title' WHERE `attribute_code` ='meta_title';
UPDATE `eav_attribute` SET `frontend_input` = 'select' WHERE `attribute_code`='display_mode';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

