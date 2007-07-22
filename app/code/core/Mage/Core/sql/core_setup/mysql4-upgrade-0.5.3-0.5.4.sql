/**
 * prepare store email templates
 */

replace into core_config_field (`path`, `frontend_label`, `frontend_type`, `source_model`) values ('email','Email','text',''),
('email/templates','System Templates','text',''),
('email/templates/subscription_confirm','Confirmation subscription message','select','adminhtml/newsletter_config_source_template'),
('email/templates/wishlist_share_message','Wishlist sharing message','select','adminhtml/newsletter_config_source_template');
