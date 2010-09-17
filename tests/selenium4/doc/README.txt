How To Write Selenium Tests Using Selenium Framework
====================================================
Brief Guide

1. Overall Architecture and Settings

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

1.1. Framework Configuration

All configuration files used in the framework are well-formed XML with simple structure. Please edit XML files carefully, be sure you closed every open tag, always follow rules for encoding a well-formed XML file. In particular, always escape special chars and entities like ampersand, '<' and '>' signs and so on. In case you need to put a big portion of text into certain node and you are not sure it's XML compliant, always use <![CDATA[ ... ]]> mechanism.

So what can you add or modify in the config/ directory?

- When you need to edit the main configuration, you do it by editing config/config.xml. 
- In case you want to update test case reference, edit config/reference.xml.
- In case you want to edit some specific configuration file, edit config.xml under one of the env directories.
- In case you want to edit some specific UI map file, edit map.xml under one of the env directories.

You can include additional XML files into any node in an existing XML file. For example, in case you need to include the content of the some-other-node.xml file into the node <someOtherNode>, use #include directive as shown below:

<?xml version="1.0" encoding="UTF-8"?>
<config>
    <someNode>Some Value</someNode>
    <someOtherNode>#include some-other-node.xml</someOtherNode>
</config>

Please note, that if the current file is located right in the config/ directory, the framework will try to include the file relatively from config/ directory. However, if the current file is located ON ANY LEVEL under config/ directory (for example, under config/env1/include/ directory), the framework will attempt to include the file relatively from it's base env directory (from config/env1/ in this case). Don't think that the location of the current file will always be a basic directory for such inclusion!


1.2. Setting Up Test Environment

The framework allows to have as many different independent configurations as you need, and use any of them with the existing test cases, but only one configuration set is considered active. In the framework terms it is called 'environment'. You can select one environment as active. It can be done in several ways:
- The environment can be passed to the loader by setting up SELENIUM_ENV command processor environment variable.
- The environment can be set as the value of the <env> node in the main config.

The first assignment takes precedence over the second. 


1.3. Setting Up Debug Level

The framework has 4 different debug levels: 
- INFO, when all errors, debug and user info messages are displayed
- DEBUG, when only errors and debug messages are displayed
- ERROR, when only errors are displayed
- SILENT, or OFF, when no messages are displayed in the output.

You can set up the debug level in several ways:
- The debug level value can be passed to the loader by setting up SELENIUM_DEBUG_LEVEL command processor environment variable.
- The debug level can be set as the value of the <debugLevel> node in the main config.

The first assignment takes precedence over the second. 


2. Test Loader

The runtest.bat script executable file is used to run the framework. You can run your test package as 
runtest <testCaseFile1> <testCaseFile2> ... <testCaseFileN>
or
 runtest <testCaseID1> [ <testCaseID2> ... [ <testCaseIDN> ] ]
where:
  - <testCaseFile> is a PHP file that contains a test case
  - <testCaseID> is an ID corresponding a test case

To use test case Ids, please edit config/references.xml. Every <testCase> node corresponds to every test case or set of test cases. It contain three child nodes:

  - <id> contains a unique ID for the item, you will use it when running test as "runtest <testCaseID>..."
  - <name> is a name of the test. It is not currently used in the system
  - <source> is a container which can have one or several test case classes, every one of them may be optionally complemented with a name of the test.
  
For example, you can use either

<testCase>
    <id>S-5</id>
    <name>SmokeTest: Site Tests</name>
    <source>Admin_Scope_Site</source>
</testCase>

or

<testCase>
    <id>S-5a</id>
    <name>SmokeTest: Site Creation</name>
    <source>Admin_Scope_Site/testSiteCreation</source>
</testCase>

or

<testCase>
    <id>S-5b</id>
    <name>SmokeTest: Site Tests and Store Creation</name>
    <source>Admin_Scope_Site,Admin_Scope_Store/testStoreCreation</source>
</testCase>

Please note, you cannot use test case ID if it isn't defined in config/references.xml properly.

