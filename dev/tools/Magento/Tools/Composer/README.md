Composer Tools
==============

A set of tools that allows creating or maintaining `composer.json` files.

Skeleton Creator
---

Creates in the root directory a `composer.json` file that aggregates all Magento components as Composer packages.

```shell
> php -f create-skeleton.php -- -help
Usage: create-skeleton.php [ options ]
--edition|-e <string> Edition of which packaging is done. Acceptable values: [ee|enterprise] or [ce|community]
--version|-v <string> Version for the composer.json file
--verbose|-r          Detailed console logs
--dir|-d <string>     Working directory of build. Default current code base.

```

Archiver
---

Breaks down a working copy into packages (zip-archives) with Magento components. Each component must already contain a `composer.json` file in its root directory.

```shell
> php -f archiver.php -- -help
Usage: archiver.php [ options ]
--verbose|-v         Detailed console logs
--output|-o <string> Generation dir. Default value _packages
--dir|-d <string>    Working directory of build. Default current code base.

```

A package for Magento edition will also be created, if there is a `composer.json` file in the root directory of Magento code base. This package will contain everything except stuff that was packaged into other packages.

Version Setter
---

Go through all composer.json files of Magento components and set their version. Can optionally update version in the dependent components.

```shell
> php -f version.php -- --version=2.1.3 [--dependent=<exact|wildcard>] [--dir=/path/to/work/dir]
--version - set the specified version value to all the components. Format: 'x.y.z' or 'x.y.z-stability.n'
--dependent - in all the dependent components, set a version of depenency
  exact - set exactly the same version as specified
  wildcard - use the specified version, but replace last number with a wildcard - e.g. 1.2.*
--dir - use specified path as the working directory

```
