#!/usr/bin/php
<?php
/**
 * Magento repository publishing script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

// get CLI options, define variables
define('USAGE', 'php -f publish.php -- [--source="<path>"] [--target="<path>"]');
$options = getopt('', array('source::', 'target::'));
$source = (isset($options['source']) ? $options['source'] : 'http://git.magento.com/magento2');
$target = (isset($options['target']) ? $options['target'] : 'http://git.magento.com/magento2_public');
define('TARGET_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'target');

// clone target and merge source into it
execVerbose('git clone %s %s', $target, TARGET_DIR);
execGit('git remote add source %s', $source);
execGit('git fetch source');
execGit('git merge --no-commit --squash source/master');
execGit('git reset');

// extrude and validate

// replace license notices and validate

// commit and publish
execGit('git add .');
execGit('git status');
execGit('git commit --message=%s', 'Merged commits from the original repository.');
execGit('git push origin master:master');

/**
 * Execute a command with automatic escaping of arguments
 *
 * @param string $command
 */
function execVerbose($command)
{
    $args = func_get_args();
    array_shift($args);
    foreach ($args as $key => $value) {
        $args[$key] = escapeshellarg($value);
    }
    $command = call_user_func_array('sprintf', array_merge(array($command), $args));
    echo $command . "\n";
    exec($command, $output, $return);
    echo implode("\n", $output) . "\n";
    if (0 !== $return) {
        exit(1);
    }
}

/**
 * Add TARGET_DIR as working copy to a git command
 *
 * @param string $command
 */
function execGit($command)
{
    $args = func_get_args();
    $command = str_replace('git ', 'git --git-dir %s --work-tree %s ', $command);
    array_shift($args);
    array_unshift($args, TARGET_DIR);
    array_unshift($args, TARGET_DIR . DIRECTORY_SEPARATOR . '.git');
    array_unshift($args, $command);
    call_user_func_array('execVerbose', $args);
}
