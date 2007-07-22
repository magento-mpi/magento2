alter table `core_config_field` add unique `IDX_PATH` ( `path` );

/**
 * prepare store root_category
 */

replace into core_config_field (`path`, `frontend_label`, `frontend_type`, `source_model`) values
('catalog', 'Catalog', 'text', ''),
('catalog/category', 'Category', 'text', ''),
('catalog/category/root_id', 'Root category', 'select', 'adminhtml/system_config_source_category');
