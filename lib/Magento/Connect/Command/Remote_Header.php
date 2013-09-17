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
        'list-upgrades' => array(
            'summary' => 'List Available Upgrades',
            'function' => 'doListUpgrades',
            'shortcut' => 'lu',
            'options' => array(
                'channelinfo' => array(
                    'shortopt' => 'i',
                    'doc' => 'output fully channel-aware data, even on failure',
                    ),
            ),
            'doc' => '[preferred_state]
List releases on the server of packages you have installed where
a newer version is available with the same release state (stable etc.)
or the state passed as the second parameter.'
            ),
        'list-available' => array(
            'summary' => 'List Available Packages',
            'function' => 'doListAvailable',
            'shortcut' => 'la',
            'options' => array(
                'channel' =>
                    array(
                    'shortopt' => 'c',
                    'doc' => 'specify a channel other than the default channel',
                    'arg' => 'CHAN',
                    ),
                'channelinfo' => array(
                    'shortopt' => 'i',
                    'doc' => 'output fully channel-aware data, even on failure',
                    ),
                ),
            'doc' => '
Lists the packages available on the configured server along with the
latest stable release of each package.',
            ),
        'download' => array(
            'summary' => 'Download Package',
            'function' => 'doDownload',
            'shortcut' => 'd',
            'options' => array(
                'nocompress' => array(
                    'shortopt' => 'Z',
                    'doc' => 'download an uncompressed (.tar) file',
                    ),
                ),
            'doc' => '<package>...
Download package tarballs.  The files will be named as suggested by the
server, for example if you download the DB package and the latest stable
version of DB is 1.6.5, the downloaded file will be DB-1.6.5.tgz.',
            ),
        'clear-cache' => array(
            'summary' => 'Clear Web Services Cache',
            'function' => 'doClearCache',
            'shortcut' => 'cc',
            'options' => array(),
            'doc' => '
Clear the XML-RPC/REST cache.  See also the cache_ttl configuration
parameter.
',
            ),
        ); 