/**
 * prepare for design packages
 */

replace into core_config_data (scope, scope_id, path, `value`) values
('general', 0, 'system/filesystem/design', '{{app_dir}}/design')
,('general', 0, 'design/package/name', 'default')
,('general', 0, 'design/package/area', 'frontend')
,('general', 0, 'design/package/theme', 'default')
,('general', 0, 'web/url/skin', '{{base_path}}skin/')
;