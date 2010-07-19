#!/usr/bin/php
<?php

$workingDir = $argv[1];
$logFileName = $workingDir . '/svnmerge-commit-message.txt';

exec(sprintf('svnmerge.py avail %s', $workingDir), $output, $exitCode);
$revisionArray = generateRevs(explode(',', $output[0]));

foreach ($revisionArray as $revision) {
    exec(sprintf('svnmerge.py merge -r %s -f %s %s', $revision, $logFileName, $workingDir), $output, $exitCode);
    if ($exitCode) {
         return $exitCode;
    }
    exec(sprintf('svn ci -F %s %s', $logFileName, $workingDir), $output, $exitCode);
    if ($exitCode) {
         return $exitCode; 
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

exit;

