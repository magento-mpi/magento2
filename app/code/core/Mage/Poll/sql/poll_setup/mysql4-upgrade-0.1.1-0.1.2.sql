SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


INSERT INTO `poll` (`poll_id`, `poll_title`, `votes_count`, `website_id`, `date_posted`, `date_closed`, `status`) VALUES 
(2, 'What is the best e-commerce solution?', 12, 1, '2007-06-11 17:07:50', NULL, 0),
(1, 'What is the most terrible e-commerce solution?', 0, 1, '2007-06-12 22:05:14', NULL, 0);

INSERT INTO `poll_answer` (`answer_id`, `poll_id`, `answer_title`, `votes_count`) VALUES 
(1, 2, 'Magento', 10),
(2, 2, 'Magento', 2),
(3, 1, 'OsCommerce', 0),
(4, 1, 'OsCommerce', 0);


INSERT INTO `poll_vote` (`vote_id`, `poll_id`, `poll_answer_id`, `ip_address`, `customer_id`) VALUES 
(1, 2, 1, 3232235528, NULL),
(4, 2, 2, 3232235528, NULL),
(5, 2, 1, 3232235528, NULL),
(6, 2, 1, 3232235528, NULL),
(7, 2, 1, 3232235528, NULL),
(8, 2, 1, 3232235528, NULL),
(9, 2, 1, 3232235528, NULL),
(10, 2, 1, 3232235528, NULL),
(11, 2, 1, 3232235528, NULL),
(12, 2, 1, 3232235528, NULL),
(13, 2, 1, 3232235528, NULL),
(15, 2, 2, 3232235528, NULL);



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
