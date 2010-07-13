<?php
	//$_SERVER = $_SERVER;
	
	//echo $_SERVER['host'];
	//echo $_SERVER['user'];
	//echo $_SERVER['pass'];
	class Generator
	{
		private $connection;
		private $server = array();
		private $defdbname;

		private function connectToDb($dbname)
		{
			//return new PDO(
			//	"mysql:host={$_SERVER['host']};dbname={}",
			//	"{$_SERVER['user']}",
			//	"{$_SERVER['pass']}",
			//	array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
			//);
			
			return new PDO(
				"mysql:host={$this->server[1]};dbname={$dbname}",
				"{$this->server[2]}",
				"{$this->server[3]}",
				array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
			);
		}
		private function createMagentoMetadata()
		{

		$this->connectToDb('information_schema')->exec("USE information_schema;");

		$this->connectToDb('information_schema')->exec("DROP DATABASE {$this->server[6]} IF EXISTS {$this->server[6]};");

		$this->connectToDb('information_schema')->exec("CREATE DATABASE {$this->server[6]};");

		$this->connectToDb('information_schema')->exec("USE {$this->server[6]};");

			$this->connectToDb($this->server[6])->exec("CREATE TABLE `modules` (
				`module_name` varchar(255) NOT NULL DEFAULT '',
				PRIMARY KEY (`module_name`)
			);");

			$this->connectToDb($this->server[6])->exec("CREATE TABLE `modules_tables` (
				`module_name` VARCHAR(255) NOT NULL DEFAULT '',
				`table_name` VARCHAR(255) NOT NULL DEFAULT '',
				PRIMARY KEY (`module_name`,`table_name`),
				CONSTRAINT `fk_modules_tables_modules` FOREIGN KEY (`module_name`) REFERENCES `modules` (`module_name`)
			);");
			
		$this->connectToDb($this->server[6])->exec("
			CREATE VIEW `referential_constraints` 
			AS 
			SELECT `referential_constraints`.`CONSTRAINT_CATALOG` AS `CONSTRAINT_CATALOG`,
				`referential_constraints`.`CONSTRAINT_SCHEMA` AS `CONSTRAINT_SCHEMA`,
				`referential_constraints`.`CONSTRAINT_NAME` AS `CONSTRAINT_NAME`,
				`referential_constraints`.`UNIQUE_CONSTRAINT_CATALOG` AS `UNIQUE_CONSTRAINT_CATALOG`,
				`referential_constraints`.`UNIQUE_CONSTRAINT_SCHEMA` AS `UNIQUE_CONSTRAINT_SCHEMA`,
				`referential_constraints`.`UNIQUE_CONSTRAINT_NAME` AS `UNIQUE_CONSTRAINT_NAME`,
				`referential_constraints`.`MATCH_OPTION` AS `MATCH_OPTION`,
				`referential_constraints`.`UPDATE_RULE` AS `UPDATE_RULE`,
				`referential_constraints`.`DELETE_RULE` AS `DELETE_RULE`,
				`referential_constraints`.`TABLE_NAME` AS `TABLE_NAME`,
				`referential_constraints`.`REFERENCED_TABLE_NAME` AS `REFERENCED_TABLE_NAME` 
			FROM `information_schema`.`referential_constraints` 
			WHERE (`referential_constraints`.`CONSTRAINT_SCHEMA` = '{$this->server[5]}')");
		
		$this->connectToDb($this->server[6])->exec("
			CREATE VIEW `vw_modules_tables` 
			AS 
			SELECT DISTINCT 
				`{$this->server[6]}`.`modules_tables`.`table_name` AS `table_name`,
				`{$this->server[6]}`.`modules_tables`.`module_name` AS `module_name`,
				1 AS `code_group` 
			FROM `{$this->server[6]}`.`modules_tables` 
			UNION 
			SELECT DISTINCT 
				`rc`.`REFERENCED_TABLE_NAME` AS `table_name`,
				`mt`.`module_name` AS `module_name`,
				2 AS `code_group` 
			FROM (`{$this->server[6]}`.`modules_tables` `mt` 
			JOIN `{$this->server[6]}`.`referential_constraints` `rc` ON((`mt`.`table_name` = `rc`.`TABLE_NAME`))) 
			UNION 
			SELECT DISTINCT 
				`rc`.`TABLE_NAME` AS `table_name`,
				`mt`.`module_name` AS `module_name`,
				2 AS `code_group` 
			FROM (`{$this->server[6]}`.`modules_tables` `mt` 
			JOIN `{$this->server[6]}`.`referential_constraints` `rc` ON((`mt`.`table_name` = `rc`.`REFERENCED_TABLE_NAME`)))");
		}
		
		private function createMapping($mode, $folder, $xmlPathPart) 
		{
			$configFile = "{$this->server[4]}/app/code/core/{$mode}/{$folder}/etc/config.xml";
			$fp = fopen($configFile, "r");
			$configXml = fread($fp, filesize($configFile));
			fclose($fp);
			$xml = new SimpleXMLElement($configXml);	
			$lfolder = strtolower($folder);
			$result = $xml->xpath("/config/global/models/{$xmlPathPart}/*");
			
			while(list( , $node) = each($result)) {
				
				foreach ($node as $node1){    
					/*
					 *	preg_match('/value_prefix/', $node1->getName(),  $match, PREG_OFFSET_CAPTURE);
					 *	if (empty($match)) {
					 *		$isLike = '';
					 *	} else {
					 *		$isLike = '%';
					 *	}
					 */
					$isLike = '%';
					foreach ($node1 as $node2) {
						$node2 = $this->server[7] . $node2;
                                                $sql = "INSERT INTO modules_tables (module_name, table_name) " . 
							" SELECT '{$folder}', t1.table_name " . 
							" FROM information_schema.tables t1 " .
							" WHERE t1.table_schema = '{$this->server[5]}' " . 
							" AND t1.table_name LIKE '{$node2}{$isLike}' " . 
							" AND NOT EXISTS (SELECT 1 FROM modules_tables t2 WHERE t2.table_name = t1.table_name); \n";
							echo $sql . "<br />";
						echo "\n";
						$this->connection->exec($sql); 
					}
				}
			}
		}
		

		public function main()
		{
			$this->server = $_SERVER['argv'];
			$this->createMagentoMetadata();
			$this->connection = $this->connectToDB($this->server[6]);
			$dir	=  $this->server[4].'/app/code/core/Mage';
			$modulesFolders		= scandir($dir);
			//var_dump($modulesFolders);
			
			unset ($modulesFolders[0]);
			unset ($modulesFolders[1]);
			unset ($modulesFolders[2]);
			
		
			foreach ($modulesFolders as $folder) {
				//echo "INSERT INTO modules (module_name) VALUES ('{$folder}');" . "<br />";
				$this->connection->exec("INSERT INTO modules (module_name) VALUES ('{$folder}');");
				echo "MODULES";
				$lfolder = strtolower($folder);
			
				$this->createMapping('Mage',$folder,"{$lfolder}_mysql4");
				$this->createMapping('Mage',$folder,"{$lfolder}_resource");
				$this->createMapping('Mage',$folder,"{$lfolder}_resource_eav_mysql4");
				$this->createMapping('Mage',$folder,$lfolder);
				$this->createMapping('Mage',$folder,"{$lfolder}_entity");		
			}			
		
			$dir	= $this->server[4].'/app/code/core/Enterprise';
			$modulesFolders		= scandir($dir);
			//echo "<pre>";
			
			//var_dump($modulesFolders);
			
			unset ($modulesFolders[0]);
			unset ($modulesFolders[1]);
			unset ($modulesFolders[2]);			

			foreach ($modulesFolders as $folder) {
				//echo "INSERT INTO modules (module_name) VALUES ('{$folder}');" . "<br />";
				//$this->connection->exec("INSERT INTO modules (module_name) VALUES ('{$folder}');");
				$lfolder = strtolower($folder);
			
				$this->createMapping('Enterprise',$folder,"enterprise_{$lfolder}_mysql4");
				$this->createMapping('Enterprise',$folder,"enterprise_{$lfolder}_resource");
				$this->createMapping('Enterprise',$folder,"enterprise_{$lfolder}_resource_eav_mysql4");
				$this->createMapping('Enterprise',$folder,"enterprise_{$lfolder}");
				$this->createMapping('Enterprise',$folder,"enterprise_{$lfolder}_entity");					
						
			}
		}	
	}	
	

	$test = new Generator;	
	$test->main();
	var_dump($_SERVER);
//http://localhost/generator.php?host=localhost&user=schemaspy&pass=123123q&apppath=/1.8.x
?>
