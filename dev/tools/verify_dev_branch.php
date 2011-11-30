<?php
/**
 * A tool that monitors health of origin/dev branch versus origin/master or topic branches
 *
 * Exits with error code 1 and no messages if there is any execution error.
 * Exits with health report for the origin/dev branch. If healthy -- with code 0, otherwise 1.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     tools
 * @copyright   {copyright}
 * @license     {license_link}
 */

// setup environment variables
$baseDir = realpath(__DIR__ . '/../..');
$gitDir = sprintf('--git-dir=%s --work-tree=%s',
    escapeshellarg($baseDir . DIRECTORY_SEPARATOR . '.git'), escapeshellarg($baseDir)
);

// determine list of topic branches
exec("git {$gitDir} branch -r", $branches, $code);
$code == 0 || exit(1);
foreach ($branches as $key => $value) {
    if (preg_match('/^\s*(origin\/MAGETWO\-.+)$/', $value, $matches)) {
        $branches[$key] = $matches[1];
    } else {
        unset($branches[$key]);
    }
}
$branches[] = 'origin/master';

// see if there are commits not merged yet to the origin/dev
$dirtyCommits = array();
$cleanBranches = $branches;
foreach ($branches as $key => $branch) {
    exec(sprintf("git {$gitDir} log --format=format:%s origin/dev..{$branch}", '%H%x09%an%x09%f'), $output, $code);
    $code == 0 || exit(1);
    foreach ($output as $commit) {
        $dirtyCommits[$commit][] = $branch;
        unset($cleanBranches[$key]);
    }
}

echo "\nThe following branches are merged properly into origin/dev:\n\t";
echo implode("\n\t", $cleanBranches) . "\n";
if (!$dirtyCommits) {
    exit(0);
}

echo "\nThe following commits are missing in origin/dev branch, but detected in topic or master branches:\n\n";
foreach ($dirtyCommits as $commit => $branches) {
    echo $commit . "\n\t" . implode("\n\t", $branches) . "\n\n";
}
exit(1);
