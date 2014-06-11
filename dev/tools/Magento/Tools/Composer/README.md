composer-packager
=================

A tool that helps you package magento source code into composer packages.

Uses
---
This tool uses the package identifier of each package, and create a composer package based on that information. 
The dependency's version are determined based on the information provided in identifier file of those packages. 
The versions are modified to wrap upto 4 decimals. This will soon be fixed when the new versioning policy takes place.
It also includes script to archive zip each package, and store it in a specified location.


Help
---

```shell
> php -f create-composer.php -- -help
Usage: create-composer.php [ options ]
--edition|-e <string> Edition of which packaging is done. Acceptable values: [ee|enterprise] or [ce|community]
--verbose|-v          Detailed console logs
--clean|-c            Clean composer.json files from each component

```

```shell
> php -f archiver.php -- -help
Usage: archiver.php [ options ]
--verbose|-v         Detailed console logs
--output|-o <string> Generation dir. Default value _packages

```