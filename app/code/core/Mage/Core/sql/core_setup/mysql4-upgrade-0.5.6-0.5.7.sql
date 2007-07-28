replace into core_config_field (`path`, `frontend_label`, `frontend_type`, `source_model`) values
('web/url/upload','Upload files URL', '','');

replace into core_config_data (scope, scope_id, path, `value`) values
('default', 0, 'web/url/upload', '{{base_path}}media/upload/');

