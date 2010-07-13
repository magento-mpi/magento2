SET group_concat_max_len =4096;
SELECT 
CONCAT('<html>',
	'<body>',
REPLACE(
GROUP_CONCAT('<a href="', 'magento/', m.module_name, '/index.html" target="data">', m.module_name, '</a>'),
	',', '<br />'),
	'</body>',
	'</html>') AS "<!--List of modules-->"
FROM (
SELECT 'All' AS module_name
UNION ALL 
SELECT module_name
FROM modules	
	) m 
WHERE EXISTS (
	SELECT 1
	FROM modules_tables t  
	WHERE m.module_name = t.module_name OR m.module_name = 'All');