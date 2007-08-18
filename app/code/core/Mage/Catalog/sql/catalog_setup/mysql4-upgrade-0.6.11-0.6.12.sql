SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

UPDATE `catalog_product_type` SET `code` = 'Simple Product' WHERE `type_id` =1;

UPDATE `catalog_product_type` SET `code` = 'Configurable Super Product' WHERE `type_id` =3;

UPDATE `catalog_product_type` SET `code` = 'Grouped Super Product' WHERE `type_id` =4;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

