<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once __DIR__ . '/../../tools/Magento/Tools/Composer/Package/Version.php';

/**
 * Execute a command with automatic escaping of arguments
 *
 * @param string $command
 * @return array
 * @throws Exception
 */
function execVerbose($command)
{
    $args = func_get_args();
    $args = array_map('escapeshellarg', $args);
    $args[0] = $command;
    $command = call_user_func_array('sprintf', $args);
    echo $command . PHP_EOL;
    exec($command, $output, $exitCode);
    foreach ($output as $line) {
        echo $line . PHP_EOL;
    }
    if (0 !== $exitCode) {
        throw new Exception("Command has failed with exit code: $exitCode.");
    }
    return $output;
}

/**
 * Get the top section of a text in markdown format
 *
 * @param string $contents
 * @return string
 * @throws Exception
 * @link http://daringfireball.net/projects/markdown/syntax
 */
function getTopMarkdownSection($contents)
{
    $parts = preg_split('/^[=\-]+\s*$/m', $contents);
    if (!isset($parts[1])) {
        throw new Exception("No commit message found in the changelog file.");
    }
    list($version, $body) = $parts;
    $version = trim($version);
    \Magento\Tools\Composer\Package\Version::validate($version);
    $body = explode("\n", trim($body));
    array_pop($body);
    $body = implode("\n", $body);
    return $version . "\n" . $body;
}

/**
 * Get Magento user name for public GitHub repository
 *
 * @return string
 */
function getGitUsername()
{
    return 'mage2-team';
}

/**
 * Get Magento user e-mail for public GitHub repository
 *
 * @return string
 */
function getGitEmail()
{
    return 'mage2-team@magento.com';
}
