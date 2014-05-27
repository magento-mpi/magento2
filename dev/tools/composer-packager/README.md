composer-packager
=================

A tool that helps you pacakge magento source code into composer packages. 

Uses
---
This tool uses the package identifier of each package, and create a composer package based on that information. 
The dependency's version are determined based on the information provided in identifier file of those packages. 
The versions are modified to wrap upto 4 decimals. This will soon be fixed when the new versioning policy takes place.
It also includes script to archive zip each package, and store it in a specified location.

Instructions
---
The tool is located at dev/tools/composer-packager 

Help
---

```shell
> php -f create-composer.php -- -help
Usage: create-composer.php [ options ]
--verbose|-v         Detailed console logs
--clean|-c           Clean composer.json files from each component

```

```shell
> php -f archiver.php -- -help
Usage: archiver.php [ options ]
--verbose|-v         Detailed console logs
--output|-o <string> Generation dir. Default value magento2/dev/tools/composer-packager/packages

```