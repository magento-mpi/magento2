FormatCode.php

FormatCode must be run from this directory.

Usage:

php FormatCode.php --quiet --display --blacklist=<blacklist file> --filelist=<file list> --root=<root directory> [file list]

Where:

-q or --quiet: Surpresses some output
-d or --display: Only display the files to be modified without making changes
-b=<> or --blacklist=<blacklist file>: specify the name of a file containing items not to process
-f=<> or --filelist=<file list>: specify the name of a file containing items to process
-r=<> or --root=<root directory>: specify the root directory used to locate the files
[file list]: optional list of files to process in addition to or instead of those indicated with the --filelist option

==================================

RunCodeSniffer.php

RunCodeSniffer must be run from the root directory.

Usage:

php dev\tools\Magento\Tools\Formatter\RunCodeSniffer.php -phpcs=<phpcs file> -blacklist=<blacklist file> -filelist=<file list>

-phpcs=<phpcs file>: specify reference to local code sniffer file
-blacklist=<blacklist file>: specify the name of a file containing items not to process
-filelist=<file list>: specify the name of a file containing items to process

All other options will be passed directly to phpcs.
