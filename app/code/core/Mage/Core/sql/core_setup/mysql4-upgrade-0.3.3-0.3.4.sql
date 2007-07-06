/*
VIM + /dev/hands
*********************************************************************
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

ALTER TABLE `admin_assert`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `admin_role`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `admin_rule`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `admin_user`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `catalog_category_filter`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `catalog_product_store`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `catalog_product_type`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `customer_entity_datetime`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `customer_entity_decimal`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `customer_entity_int`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `customer_entity_text`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `customer_entity_varchar`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `customer_log`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `customer_newsletter`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `customer_wishlist`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
