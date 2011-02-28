#!/usr/bin/php
<?php

$workingDir = $argv[1];
$logFileName = $workingDir . '/svnmerge-commit-message.txt';

$user = $argv[2];
$pass = $argv[3];

$resolver = isset($argv[4]) ? $argv[4] : false;

$sources = array($argv[5], $argv[6]);

$exitCode = 0;

foreach ($sources as $source) {
    echo "Merging source  $source ...\n";
    $output = array();
    $command = sprintf('svnmerge.py avail -S %s --username %s --password %s %s', $source, $user, $pass, $workingDir);
    exec($command, $output, $exitCode);
    if (!isset($output[0])) {
         echo "Nothing to merge\n";
         continue;
    }
    $revisionArray = generateRevs(explode(',', $output[0]));
    foreach ($revisionArray as $revision) {
        echo "Merging revision #" . $revision . "...";
        exec(sprintf('svn up --username %s --password %s --no-auth-cache %s', $user, $pass, $workingDir), $output, $exitCode);
        analyzeExitCode($exitCode, $output);
        exec(sprintf('svnmerge.py merge -S %s -r %s -f %s --username %s --password %s %s', $source, $revision, $logFileName, $user, $pass, $workingDir), $output, $exitCode);
        analyzeExitCode($exitCode, $output);
    
        //Checking for conflicts
        exec(sprintf('svn st %s | grep ^C', $workingDir), $output, $exitCode);
        if (!empty($output) && $resolver) {
            echo "Conflicts found!\n";
            if ($resolver) {
                echo "Trying to resolve conflicts...";
                exec(sprintf('./%s %s %s %s %s', $resolver, $workingDir, $user, $pass, $revision), $resolverOutput, $resolverExitCode);
                analyzeExitCode($resolverExitCode, $resolverOutput);
                echo "Done.\n";
            } else {
                analyzeExitCode(1, array("Resolver not specified, aborting."));
            }
        } 
   
        exec(sprintf('svn up --username %s --password %s --no-auth-cache %s', $user, $pass, $workingDir), $output, $exitCode);
        analyzeExitCode($exitCode, $output); 
        exec(sprintf('svn ci -F %s --username %s --password %s --no-auth-cache %s', $logFileName, $user, $pass, $workingDir), $output, $exitCode);
    }
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

