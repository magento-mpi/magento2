/**
 * prepare for design packages
 */

replace into core_config_data (path, `value`) values
('system/filesystem/design', '{{app_dir}}/design'),
('design/package/name', 'default'),
('design/package/area', 'frontend'),
('design/package/theme', 'default'),
