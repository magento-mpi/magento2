<?php

/**
 * Script to get changes between feature branch and the mainline
 *
 * {license_notice}
 *
 * @category   dev
 * @package    build
 * @copyright  {copyright}
 * @license    {license_link}
 */

define ('GITHUB_URL_CHANGES', 'https://github.scm.corp.ebay.com/api/v3/repos/%TEAM_REPO%/magento2/compare/magento2:develop...%FEATURE_BRANCH%');
define('USAGE', <<<USAGE
    php -f get_github_changes.php --
    --team-repo="<team_repo>" --feature-branch="<feature_branch>"
    [--output-file="<output_file>"] [--file-formats="<comma_separated_list_of_formats>"]

USAGE
);

$options = getopt('', array(
    'team-repo:', 'feature-branch:', 'output-file:', 'file-formats:',
));
if (empty($options['team-repo']) || empty($options['feature-branch'])) {
    echo USAGE;
    exit(1);
}

$outputFile = isset($options['output-file']) ? $options['output-file'] : 'changed_files.txt';
$fileFormats = explode(',', isset($options['file-formats']) ? $options['file-formats'] : 'php');

$changes = retrieveChangesAcrossForks($options['team-repo'], $options['feature-branch']);
$changedFiles = getChangedFiles($changes, $fileFormats);
generateChangedFilesList($outputFile, $changedFiles);

function generateChangedFilesList($outputFile, $changedFiles)
{
    $changedFilesList = fopen($outputFile, 'w');
    foreach ($changedFiles as $file) {
        fwrite($changedFilesList, $file . PHP_EOL);
    }
    fclose($changedFilesList);
}

function getChangedFiles($changes, $fileFormats)
{
    $files = array();
    foreach($changes as $change) {
        foreach($fileFormats as $format) {
            if(strpos($change['filename'], '.' . $format))
                $files[] = $change['filename'];
        }
    }

    return $files;
}

function retrieveChangesAcrossForks($teamRepo, $featureBranch)
{
    $githubChangesUrl = str_replace('%FEATURE_BRANCH%', $featureBranch, str_replace('%TEAM_REPO%', $teamRepo, GITHUB_URL_CHANGES));

    $request = curl_init($githubChangesUrl);
    curl_setopt($request , CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($request , CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($request);
    curl_close($request);

    if ($response === FALSE) return;

    $jsonResponse = json_decode($response, true);
    if (count($jsonResponse) > 0)
        return $jsonResponse['files'];
}
