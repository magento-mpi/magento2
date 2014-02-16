<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

    $commands = array(
        'list-installed' => array(
            'summary' => 'List Installed Packages In The Default Channel',
            'function' => 'doList',
            'shortcut' => 'l',
            'options' => array(
                'channel' => array(
                    'shortopt' => 'c',
                    'doc' => 'list installed packages from this channel',
                    'arg' => 'CHAN',
                    ),
                'allchannels' => array(
                    'shortopt' => 'a',
                    'doc' => 'list installed packages from all channels',
                    ),
                ),
            'doc' => '<package>
If invoked without parameters, this command lists the PEAR packages
installed in your php_dir ({config php_dir}).  With a parameter, it
lists the files in a package.
',
            ),
        'list-files' => array(
            'summary' => 'List Files In Installed Package',
            'function' => 'doFileList',
            'shortcut' => 'fl',
            'options' => array(),
            'doc' => '<package>
List the files in an installed package.
'
            ),
        'info' => array(
            'summary'  => 'Display information about a package',
            'function' => 'doInfo',
            'shortcut' => 'in',
            'options'  => array(),
            'doc'      => '<package>
Displays information about a package. The package argument may be a
local package file, an URL to a package file, or the name of an
installed package.'
            )
        ); 