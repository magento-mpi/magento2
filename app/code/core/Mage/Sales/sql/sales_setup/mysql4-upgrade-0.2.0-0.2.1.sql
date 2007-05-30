
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


drop table if exists `sales_quote_rule`;
create table `sales_quote_rule` (
quote_rule_id int unsigned not null auto_increment primary key,
rule_name varchar(255) not null,
rule_description text not null,
sort_order smallint not null,
conditions_serialized text,
actions_serialized text,
index (rule_name),
index (sort_order)
) engine=innodb default charset=utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
