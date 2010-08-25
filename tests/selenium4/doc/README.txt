How To Write Selenium Tests Using Selenium Framework
====================================================
Brief Guide

1. Overall Architecture

Selenium Framework maintains the following directory structure:

basedir
+ config
  + env_dir1
  + env_dir2
  + env_dirN
  config.xml
  references.xml
+ doc
+ lib
+ models
+ tests
runtest.bat
testloader.php

All configuration files used in the framework are well-formed XML with simple structure. Please edit XML files carefully, be sure you closed every open tag, always follow rules for encoding a well-formed XML file. In particular, always escape special chars and entities like ampersand, '<' and '>' signs and so on. In case you need to put a big portion of text into certain node and you are not sure it's XML compliant, always use <![CDATA[ ... ]]> mechanism.

So what can you add or modify in the config/ directory?

- When you need to edit the main configuration, you do it by editing config/config.xml. 
- In case you want to update test case reference, edit config/reference.xml.
- In case you 