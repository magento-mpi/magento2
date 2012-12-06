<?php
/**
 * Multi-tenant build deployment script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('USAGE', <<<USAGE
php -f deploy.php --
    --deploy-dir=<deployment_dir> --deploy-url-pattern=<url_pattern>
    [--tenant-ids=<tenant_ids>] [--new-install=1]
    --dsn=mysql://<db_user>:<db_password>@<db_host>

USAGE
);
define('DB_NAME_PATTERN', 'saas_qa_tenant_%s');
define('LOCAL_XML_PATTERN', 'local.%s.xml');

$options = getopt('', array('deploy-dir:', 'deploy-url-pattern:', 'dsn:', 'tenant-ids::', 'new-install::', 'unix::'));
if (empty($options['deploy-dir']) || empty($options['deploy-url-pattern']) || empty($options['dsn'])) {
    echo USAGE;
    exit(1);
}

try {
    // deployment parent directory
    $deployParent = realpath(dirname($options['deploy-dir']));
    if (!$deployParent || !is_dir($deployParent) || !is_writable($deployParent)) {
        throw new Exception("Deployment parent dir cannot be resolved or not writable: '{$options['deploy-dir']}'");
    }
    $deployDir = $options['deploy-dir'];

    // deployment URL pattern
    $urlPattern = $options['deploy-url-pattern'];
    if (!preg_match('/^https?:\/\/\*\.tenant\..+$/', $urlPattern)) {
        throw new Exception("Invalid deployment URL pattern: '{$options['deploy-url-pattern']}'");
    }

    // database connection string
    $dsn = parse_url($options['dsn']);
    if (!$dsn || empty($dsn['host']) || 'mysql' !== $dsn['scheme'] || empty($dsn['user'])) {
        throw new Exception("Malformed database connection string: '{$options['dsn']}'");
    }

    // tenant ids
    $tenantsFile = $deployParent . '/tenants.txt';
    $tenantIds = array();
    if (!empty($options['tenant-ids'])) {
        $ids = explode(',', $options['tenant-ids']);
        foreach ($ids as $id) {
            if (!preg_match('/^[a-z0-9]+$/', $id)) {
                throw new Exception("Invalid tenant ID: '{$id}'");
            }
            $tenantIds[] = $id;
        }
    } else {
        $tenantIds = explode(',', file_get_contents($tenantsFile));
    }
    if (empty($tenantIds)) {
        throw new Exception("No tenants identified to install or update");
    }

    require __DIR__ . '/../../../app/autoload.php';
    Magento_Autoload_IncludePath::addIncludePath(array(__DIR__ . '/../../../lib'));
    $shell = new Magento_Shell(true);
    $workingDir = realpath(__DIR__ . '/../../..');

    if (!empty($options['new-install'])) {
        // recreate deployment dir
        if (is_dir($deployDir)) {
            rmDirRecursive($deployDir, $shell);
        }
        $shell->output("Creating deployment directory: '{$deployDir}'");
        mkdir($deployDir);

        // clone git repository from working copy to the deployment dir
        $shell->execute('git clone %s %s', array($workingDir, $deployDir));
    } elseif (!is_dir($deployDir) || !is_writable($deployDir)) {
        throw new Exception("Deployment directory does not exist or not writable: '{$deployDir}'");
    }

    // close access to all entry points
    $shell->output('Closing access to entry points...');
    touch($deployDir . '/maintenance.flag');

    // install per tenant
    $dbPassword = empty($dsn['pass']) ? '' : $dsn['pass'];
    $pdo = new PDO('mysql:host=' . $dsn['host'], $dsn['user'], $dbPassword);
    $tenantUrls = array();
    foreach ($tenantIds as $tenantId) {
        // recreate DB
        $tenantDb = sprintf(DB_NAME_PATTERN, $tenantId);
        $pdo->query("DROP DATABASE IF EXISTS `{$tenantDb}`");
        $pdo->query("CREATE DATABASE `{$tenantDb}`");

        // recreate file system
        $tenantVarDir = $deployDir . '/var.' . $tenantId;
        rmDirRecursive($tenantVarDir, $shell);
        $shell->output("Creating var directory: '{$tenantVarDir}'");
        mkdir($tenantVarDir);
        $tenantMediaDir = $deployDir . '/pub/media.' . $tenantId;
        $shell->output("Creating media directory: '{$tenantMediaDir}'");
        mkdir($tenantMediaDir);

        // run install.php and obtain generated base configuration
        $tenantUrl = str_replace('*', $tenantId, $urlPattern);
        $tenantUrls[] = $tenantUrl;
        $options = array(
            'license_agreement_accepted' => 'yes',
            'locale'                     => 'en_US',
            'timezone'                   => 'America/Los_Angeles',
            'default_currency'           => 'USD',
            'db_host'                    => $dsn['host'],
            'db_name'                    => $tenantDb,
            'db_user'                    => $dsn['user'],
            'db_pass'                    => $dbPassword,
            'use_secure'                 => 'yes',
            'use_secure_admin'           => 'yes',
            'admin_no_form_key'          => 'yes',
            'use_rewrites'               => 'no',
            'admin_lastname'             => $tenantId,
            'admin_firstname'            => $tenantId,
            'admin_email'                => "{$tenantId}@example.com",
            'admin_username'             => $tenantId,
            'admin_password'             => '123123q',
            'url'                        => $tenantUrl,
            'skip_url_validation'        => true,
            'secure_base_url'            => $tenantUrl,
            'session_save'               => 'db',
            'cleanup_database'           => true,
        );
        $command = 'php -f %s --';
        $arguments = array("{$deployDir}/dev/shell/install.php");
        foreach ($options as $key => $value) {
            $command .= ' --%s %s';
            $arguments[] = $key;
            $arguments[] = $value;
        }
        $shell->execute($command, $arguments);
        // hack to get the local.xml out of the code base (entire installer requires refactoring to make it clean)
        $tenantConfigFile = $deployParent . '/' . sprintf(LOCAL_XML_PATTERN, $tenantId);
        $shell->output("Moving 'app/etc/local.xml' out of the code base to '{$tenantConfigFile}'");
        rename("{$deployDir}/app/etc/local.xml", $tenantConfigFile);
    }

    // copy fresh index.build.php into the deployment dir and hack the .htaccess to not impose index.php
    $entryPoint = $deployDir . '/index.build.php';
    $shell->output("Copying custom entry point to '{$entryPoint}'");
    copy($workingDir . '/dev/build/saas_qa/index.build.php', $entryPoint);
    $htaccess = $deployDir . '/.htaccess';
    $shell->output("Copying custom .htaccess '{$htaccess}'");
    copy($workingDir . '/dev/build/saas_qa/.htaccess', $htaccess);

    // open entry points
    $shell->output('Opening access to entry points...');
    unlink($deployDir . '/maintenance.flag');

    // output all URLs available for all tenants to the log
    $shell->output("Deployed tenant URLs:\n" . implode("\n", $tenantUrls));

    // write file "tenants.txt" in directory outside of deployment dir and persist all tenant IDs for further reuse
    $shell->output("Tenant IDs are recorded to the file '{$tenantsFile}' for use in future builds.");
    file_put_contents($tenantsFile, implode(',', $tenantIds));

} catch (Exception $e) {
    echo $e;
    exit(1);
}

function rmDirRecursive($dir, Magento_Shell $shell)
{
    $isWindows = '\\' == DIRECTORY_SEPARATOR;
    if ($isWindows) {
        if (is_dir($dir)) {
            $shell->execute('rmdir /S /Q %s', array($dir));
        }
    } else {
        $shell->execute('rm -rf %s', array($dir));
    }
}
