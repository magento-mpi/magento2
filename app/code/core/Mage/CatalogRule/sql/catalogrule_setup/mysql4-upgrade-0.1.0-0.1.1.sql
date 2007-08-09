alter table `catalogrule` 
    ,add column `stop_rules_processing` tinyint (1) DEFAULT '1' NOT NULL  after `actions_serialized`;