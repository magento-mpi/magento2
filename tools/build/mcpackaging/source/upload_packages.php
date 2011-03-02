#!/usr/bin/php
<?php
/**
 * Upload extension packages to Magento Connect 2.0 Channel Server
 *
 * Usage:
 *   upload_package.php [path [channel [channel_server_url [user_auth_id user_secret_key]]]]
 * Where:
 *   path               - path to extensions or path/name for one extension
 *   channel            - channel name. Ex.: community (default)
 *   channel_server_url - Ex.: http://connect.kiev-dev (default)
 *   user_auth_id       - auth information to channel server
 *   user_secret_key    - auth information to channel server
 */

define('LOG_DIR', '../log');
define('LOG_TO_DISPLAY', false);
define('LOG_ENABLED', false);
ini_set('memory_limit','150M');
ini_set('max_execution_time', 0);
include_once 'variengateway.php';

error_reporting(E_ALL);

if (isset($argv[1]) && in_array(strtolower(trim($argv[1],'- /')), array('usage','h','help','?')) ) {
echo(' Usage:
   upload_package.php [path [channel [server_url [user_id user_secret_key]]]]
 Where:
       path               - path to extensions or path/name for one extension
       channel            - channel name. Ex.: community (default)
       channel_server_url - URL to channel server or it\'s label (test or live)
                            For ex.: http://connect.kiev-dev (default)
       user_auth_id       - auth information to channel server
       user_secret_key    - auth information to channel server

  By default runs:
    upload_package.php ./community community http://connect.kiev-dev

');
exit(0);
}

/**
 * default values for options
 *
 * The options in arguments order. Do not remove 'script_name' option ($argv[0]).
 * @var $options array
 */
$options = array(
    'script_name'           => '',
    'path'                  => './community',
    'channel'               => 'community',
    'channel_server_url'    => 'http://connect.kiev-dev',
    'user_auth_id'          => '973e94dac41ba9d1fc9f5b818505610a',
    'user_secret_key'       => '01ead244abefd7219160773fe28a1a17',
);

if (isset($argv[3]) && $argv[3] == 'test') {
    unset($argv[3]);
    unset($argv[4]);
    unset($argv[5]);
}

if (isset($argv[3]) && $argv[3] == 'live') {
    unset($argv[3]);
    unset($argv[4]);
    unset($argv[5]);
    $options['channel_server_url']  = 'http://connect20.magentocommerce.com';
    $options['user_auth_id']        = '66d086daa3a611823b87c3efb7cb3277';
    $options['user_secret_key']     = '7a659a40edb58663fd565ca197c8bc77';
}

$options_arr = array_keys($options);
if (isset($argv) && !empty($argv)) {
    foreach ($argv as $key => $value) {
        if (isset($options_arr[$key])) {
            $options[$options_arr[$key]] = $value;
        }
    }
}

if (!file_exists($options['path'])) {
    handle_error("Directory or file not exists {$options['path']}\n");
    exit(1);
}
$files = array();
if (is_file($options['path'])) {
    $files = array(basename($options['path']));
    $options['path'] = dirname($options['path']);
} elseif (is_dir($options['path'])) {
    $files = scandir($options['path']);
} else {
    handle_error("Directory or file not exists {$options['path']}\n");
    exit(1);
}

$gtw = new VarienGateway(
            $options['channel_server_url'].'/_mgm/api.php',
            $options['user_auth_id'],
            $options['user_secret_key']
        );

handle_success('Starting to import packages to '.$options['channel_server_url']." from {$options['path']}\n");

$packageCount = 0;
foreach ($files as $module) {
    $moduleFilePath = $options['path'] . DIRECTORY_SEPARATOR . $module;
    if (
            ($module == '.') ||
            ($module == '..') ||
            !is_file($moduleFilePath) ||
            pathinfo($moduleFilePath, PATHINFO_EXTENSION) != 'tgz'
    ) {
        continue;
    }

    if (!preg_match('#^([^-]+)-(([0-9]{1,2}\.){1,4}[0-9]{1,2}[^\.]*)\.tgz$#', $module, $match)) {
        handle_error("Invalid package identifier provided: $moduleFilePath\n");
        continue;
    }

    $package = $match[1];
    $version = $match[2];

    $result = $gtw->validateRelease($moduleFilePath, $options['channel']);
    if (!empty($result->error)) {
        /**
         * If package not found then create it from package.xml.
         * If package release exist then delete old and upload new.
         */
        if (preg_match('/Package '.$package.' not found/i', $result->error)) {
            `tar xvzf $moduleFilePath package.xml`;
            if (!file_exists('package.xml')) {
                handle_error("Failed to open package.xml from $moduleFilePath.\n");
            }
            $xml = simplexml_load_file('package.xml');
            `rm -f package.xml`;
            $data = array(
                'name' => (string)$xml->name,
                'license' => (string)$xml->license,
                'licenseuri' => (string)$xml->license['uri'],
                'description' => (string)$xml->description,
                'summary' => (string)$xml->summary,
                'channel' => (string)$xml->channel,
            );
            if (!isset($data['licenseuri'])) {
                $data['licenseuri'] = ' ';
            }
            $result = $gtw->createPackage($data);
            if (!$result || isset($result->error)) {
                handle_error($result->error."\n");
                continue;
            } else {
                handle_success("Package {$options['channel']}/{$data['name']} successfully created \n");
            }
        }elseif (preg_match('/Release [0-9\.]* of '.$package.' already exists!/i', $result->error)) {
            $result = $gtw->deleteRelease($package, $version, $options['channel']);
            if (!empty($result->error)) {
                handle_error($result->error."\n");
                continue;
            }
            handle_success("Package {$options['channel']}/{$package} successfully deleted ".
                           "from server {$options['channel_server_url']}\n");
        } else {
            handle_error($result->error."\n");
            continue;
        }
    }
    $result = $gtw->isPackageExists($package, $options['channel']);
    if (!$result) {
     handle_error("Package not exist {$options['channel']}/$package \n");
     continue;
    }
    $result = $gtw->uploadRelease($moduleFilePath, $options['channel']);
    if (!empty($result->error)) {
        handle_error('1 '.$result->error."\n");
        if (!preg_match("/Release [0-9\.]* of {$options['channel']}/{$package} already exists!/i", $result->error)) {
            handle_error($result->error."\n");
            continue;
        }
    } else {
        handle_success("Release {$options['channel']}/{$package}-{$version} uploaded succesfully\n");
        ++$packageCount;
        continue;
    }
    $result = $gtw->deleteRelease($package, $version, $options['channel']);
    if (!empty($result->error)) {
        handle_error($result->error."\n");
        continue;
    }
    $result = $gtw->uploadRelease($moduleFilePath, $options['channel']);
    if (!empty($result->error)) {
        handle_error($result->error."\n");
    } else {
        handle_success("Release {$options['channel']}/{$package}-{$version} uploaded succesfully\n");
        ++$packageCount;
    }
}

handle_success("{$packageCount} {$options['channel']} packages were successfully processed \n");



function handle_error($str)
{
    LOG_ENABLED && file_put_contents(LOG_DIR.'/import_error.log', date('Y-m-d H:m:s  ').$str, FILE_APPEND);
    handle_success("[Error]: ".$str);
}

function handle_success($str)
{
    LOG_ENABLED && file_put_contents(LOG_DIR.'/import_success.log', date('Y-m-d H:m:s  ').$str, FILE_APPEND);
    if (LOG_TO_DISPLAY){
        echo $str;
    }
}
