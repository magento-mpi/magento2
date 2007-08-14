DELETE FROM `core_config_field` WHERE `path` IN('payment/verisign','payment/verisign/active','payment/verisign/order_status','payment/verisign/title','payment/verisign/sort_order','payment/verisign/user','payment/verisign/vendor','payment/verisign/partner','payment/verisign/pwd','payment/verisign/tender','payment/verisign/trxtype','payment/verisign/verbosity','payment/verisign/url');

INSERT INTO `core_config_field` (`field_id`, `level`, `path`, `frontend_label`, `frontend_type`, `frontend_class`, `frontend_model`, `backend_model`, `source_model`, `sort_order`, `show_in_default`, `show_in_website`, `show_in_store`, `module_name`) VALUES 
(342, 2, 'payment/verisign', 'Verisign', 'text', '', '', '', '', 30, 1, 1, 1, ''),
(343, 3, 'payment/verisign/active', 'Enabled', 'select', '', '', '', 'adminhtml/system_config_source_yesno', 1, 1, 1, 1, ''),
(344, 3, 'payment/verisign/order_status', 'New order status', 'select', '', '', '', 'adminhtml/system_config_source_order_status', 2, 1, 1, 1, ''),
(435, 3, 'payment/verisign/title', 'Title', 'text', '', '', '', '', 1, 1, 1, 1, ''),
(441, 3, 'payment/verisign/sort_order', 'Sort order', 'text', '', '', '', '', 100, 1, 1, 1, ''),
(534, 3, 'payment/verisign/user', 'User', 'text', '', '', '', '', 0, 1, 1, 1, ''),
(535, 3, 'payment/verisign/vendor', 'Vendor', 'text', '', '', '', '', 0, 1, 1, 1, ''),
(536, 3, 'payment/verisign/partner', 'Partner', 'text', '', '', '', '', 0, 1, 1, 1, ''),
(537, 3, 'payment/verisign/pwd', 'Password', 'text', '', '', '', '', 0, 1, 1, 1, ''),
(538, 0, 'payment/verisign/tender', 'TENDER', 'text', '', '', '', '', 0, 1, 1, 1, ''),
(540, 0, 'payment/verisign/verbosity', 'VERBOSITY', 'text', '', '', '', '', 0, 1, 1, 1, ''),
(541, 0, 'payment/verisign/url', 'URL', 'text', '', '', '', '', 0, 1, 1, 1, '');


DELETE FROM `core_config_data` WHERE `path` IN('payment/verisign','payment/verisign/active','payment/verisign/order_status','payment/verisign/title','payment/verisign/sort_order','payment/verisign/user','payment/verisign/vendor','payment/verisign/partner','payment/verisign/pwd','payment/verisign/tender','payment/verisign/trxtype','payment/verisign/verbosity','payment/verisign/url', 'payment/verisign/model');

INSERT INTO `core_config_data` (`config_id`, `scope`, `scope_id`, `path`, `value`, `old_value`, `inherit`) VALUES 
(349, 'default', 0, 'payment/verisign/active', '0', '0', 0),
(350, 'default', 0, 'payment/verisign/order_status', '1', '', 0),
(383, 'default', 0, 'payment/verisign/model', 'paygate/payflow_pro', '', 0),
(442, 'default', 0, 'payment/verisign/title', 'Credit Card (Verisign)', '', 0),
(472, 'default', 0, 'payment/verisign/sort_order', '', '', 0),
(547, 'default', 0, 'payment/verisign/user', '', '', 0),
(548, 'default', 0, 'payment/verisign/vendor', '', '', 0),
(549, 'default', 0, 'payment/verisign/partner', '', '', 0),
(550, 'default', 0, 'payment/verisign/pwd', '', '', 0),
(551, 'default', 0, 'payment/verisign/tender', 'C', '', 0),
(553, 'default', 0, 'payment/verisign/verbosity', 'MEDIUM', '', 0),
(554, 'default', 0, 'payment/verisign/url', 'https://pilot-payflowpro.verisign.com/transaction', '', 0);
