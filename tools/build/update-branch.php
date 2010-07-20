#!/usr/bin/php
<?php

$workingDir = $argv[1];
$logFileName = $workingDir . '/svnmerge-commit-message.txt';

$user = $argv[2];
$pass = $argv[3];

exec(sprintf('svnmerge.py avail --username %s --password %s %s', $user, $pass, $workingDir), $output, $exitCode);
$revisionArray = generateRevs(explode(',', $output[0]));

foreach ($revisionArray as $revision) {
    echo "Merging revision #" . $revision . "...";
    exec(sprintf('svnmerge.py merge -r %s -f %s --username %s --password %s %s', $revision, $logFileName, $user, $pass, $workingDir), $output, $exitCode);
    analyzeExitCode($exitCode, $output);
    exec(sprintf('svn ci -F %s --username %s --password %s --no-auth-cache %s', $logFileName, $user, $pass, $workingDir), $output, $exitCode);
    analyzeExitCode($exitCode, $output);
    echo "Done\n";
}

function analyzeExitCode($exitCode, $output)
{
    if ($exitCode) {
	echo "Error: " . implode("\n", $output);
	exit($exitCode);
    }
}

function generateRevs($in)
{
        $revs = array();

        foreach ($in as $rev) {
                if (strpos($rev, '-') !== FALSE) {
                        $range = explode('-', $rev);
                        $revs = array_merge($revs, range($range[0], $range[1]));
                } else {
                        $revs[] = $rev;
                }
        }
        return $revs;
}

exit(0);

