/**
 * prepare for design packages
 */

replace into core_config_data (scope, scope_id, path, `value`) values
('default', 0, 'system/filesystem/design', '{{app_dir}}/design')
,('default', 0, 'system/filesystem/skin', '{{base_dir}}/skin')
,('default', 0, 'design/package/name', 'default')
,('default', 0, 'design/package/area', 'frontend')
,('default', 0, 'design/package/theme', 'default')
,('default', 0, 'web/url/skin', '{{base_path}}skin/')
;
