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

define('GITHUB_URL_CHANGES', 'https://github.corp.ebay.com/api/v3/repos/%TEAM_REPO%/magento2/compare/magento2:develop...%FEATURE_BRANCH%');
define(
    'USAGE',
<<<USAGE
    php -f get_github_changes.php --
    --team-repo="<team_repo>" --feature-branch="<feature_branch>"
    [--output-file="<output_file>"] [--file-formats="<comma_separated_list_of_formats>"]

USAGE
);

$options = getopt('', array('team-repo:', 'feature-branch:', 'output-file:', 'file-formats:'));
if (empty($options['team-repo']) || empty($options['feature-branch'])) {
    echo USAGE;
    exit(1);
}

$outputFile = isset($options['output-file']) ? $options['output-file'] : 'changed_files.txt';
$fileFormats = explode(',', isset($options['file-formats']) ? $options['file-formats'] : 'php');

$changes = retrieveChangesAcrossForks($options['team-repo'], $options['feature-branch']);
$changedFiles = getChangedFiles($changes, $fileFormats);
generateChangedFilesList($outputFile, $changedFiles);

/**
 * Generates a file containing changed files
 *
 * @param string $outputFile
 * @param array $changedFiles
 * @return void
 */
function generateChangedFilesList($outputFile, $changedFiles)
{
    $changedFilesList = fopen($outputFile, 'w');
    foreach ($changedFiles as $file) {
        fwrite($changedFilesList, $file . PHP_EOL);
    }
    fclose($changedFilesList);
}

/**
 * Gets list of changed files
 *
 * @param array $changes
 * @param string $fileFormats
 * @return array
 */
function getChangedFiles($changes, $fileFormats)
{
    $files = array();
    foreach ($changes as $change) {
        $fileName = $change['filename'];
        foreach ($fileFormats as $format) {
            $isFileFormat = strpos($fileName, '.' . $format);
            if ($isFileFormat) {
                $files[] = $fileName;
            }
        }
    }

    return $files;
}

/**
 * Retrieves changes accross forks
 *
 * @param string $teamRepo
 * @param string $featureBranch
 * @return array
 * @throws Exception
 */
function retrieveChangesAcrossForks($teamRepo, $featureBranch)
{
    $githubChangesUrl = str_replace('%FEATURE_BRANCH%', $featureBranch, str_replace('%TEAM_REPO%', $teamRepo, GITHUB_URL_CHANGES));

    $request = curl_init($githubChangesUrl);
    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($request);
    $status = curl_getinfo($request);
    curl_close($request);

    $httpResponseCode = $status['http_code'];
    if ($httpResponseCode != 200) {
        throw new Exception("Github API call failed. The following HTTP response code was received: $httpResponseCode");
    }

    $jsonResponse = json_decode($response, true);
    $responseSize = count($jsonResponse);
    if ($responseSize > 0) {
        return $jsonResponse['files'];
    }
}
