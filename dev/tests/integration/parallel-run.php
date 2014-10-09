#!/usr/bin/env php
<?php
/**
 * {license_notice}
 *
 * @category Magento
 * @package Magento
 * @subpackage integration_tests
 * @copyright {copyright}
 * @license {license_link}
 */
define('INTEGRATION_TESTSUITE_NAME', 'Magento Integration Tests');
define('DEFAULT_PROCESS_DURATION', 1200);

$currentOptionName = false;
$cliOptions = array();
foreach ($argv as $optionNameOrValue) {
    if (substr($optionNameOrValue, 0, 2) === '--') {
        $currentOptionName = substr($optionNameOrValue, 2);
        $cliOptions[$currentOptionName] = true;
    } else {
        if ($currentOptionName) {
            $cliOptions[$currentOptionName] = $optionNameOrValue;
        }
        $currentOptionName = false;
    }
}

if (!isset($argv[1])) {
    echo 'Usage: php -f ', basename(__FILE__), ' path/to/tests -- [options]', PHP_EOL;
    echo <<<USAGE
--log-junit <file> log test execution in JUnit XML format to file
--max-instances <number> largest number of PHPUnit instances running simultaneously, 1 by default
--max-execution-time <seconds> execution time limit for each PHPUnit instance

USAGE;
    exit(1);
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
chdir(__DIR__);

$maxInstances = isset($cliOptions['max-instances']) ? (int)$cliOptions['max-instances'] : 1;
$maxExecutionTime = isset($cliOptions['max-execution-time']) ? (int)$cliOptions['max-execution-time'] : DEFAULT_PROCESS_DURATION;
if (isset($cliOptions['log-junit'])) {
    $junitLog = fopen($cliOptions['log-junit'], 'w');
    if (!$junitLog) {
        echo "Failed to open {$cliOptions['log-junit']} file for writing.", PHP_EOL;
        exit(1);
    }
    fwrite($junitLog, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<testsuites>' . PHP_EOL);
} else {
    $junitLog = false;
}

$workers = array();
for ($i = 0; $i < $maxInstances; ++$i) {
    $workers[$i] = array(
        'dir' => __DIR__,
        'idle' => true,
    );
}

require realpath(__DIR__ . '/../../../') . '/app/bootstrap.php';
$parser = new \Magento\Framework\Xml\Parser();
$parser->load('phpunit.xml.dist');
$config = $parser->xmlToArray();
$excludeList = array();
foreach ($config['phpunit']['_value']['testsuites'] as $testsuite) {
    if ($testsuite['_attribute']['name'] == INTEGRATION_TESTSUITE_NAME) {
        $excludeList = $testsuite['_value']['exclude'];
        break;
    }
}

(new \Magento\Framework\Autoload\IncludePath())->addIncludePath(__DIR__ . '/testsuite');
(new \Magento\Framework\Autoload\IncludePath())->addIncludePath(__DIR__ . '/framework');

$pathToTests = $argv[1];
$testCases = array();
foreach (glob($pathToTests, GLOB_BRACE | GLOB_ERR) ?: array() as $globItem) {
    if (is_dir($globItem)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($globItem)) as $fileInfo) {
            $pathToTestCase = (string)$fileInfo;
            if (preg_match('/Test\.php$/', $pathToTestCase) && !isTestClassAbstract($pathToTestCase)) {
                $isExcluded = false;
                foreach ($excludeList as $excludePath) {
                    if (preg_match('#' . $excludePath . '#', $pathToTestCase)) {
                        $isExcluded = true;
                        break;
                    }
                }
                if (!$isExcluded) {
                    $testCases[$pathToTestCase] = true;
                }
            }
        }
    } elseif (preg_match('/Test\.php$/', $globItem) && !isTestClassAbstract($globItem)) {
        $testCases[$globItem] = true;
    }
}


if (empty($testCases)) {
    echo "No tests cases found in the path {$pathToTests}.", PHP_EOL;
    exit(1);
}
$testCases = array_keys($testCases); // automatically avoid file duplications
sort($testCases);

$outputDir = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/var/split-by-test/');
if (!file_exists($outputDir)) {
    mkdir($outputDir);
}

echo "Testing process started with up to $maxInstances instances at the same time.", PHP_EOL;

$currentTestCase = 0;
$testCasesLeft = $testCasesCount = count($testCases);
$instancesCount = 0;
$runningProcesses = array();
$cleanExitCode = true;
$openProcessFailures = 0;

while ($testCasesLeft > 0) {
    foreach ($workers as $index => &$worker) {
        if ($worker['idle']) {
            if ($currentTestCase < $testCasesCount && $instancesCount < $maxInstances) {
                $cliArguments = " --stderr -c phpunit-{$index}.xml ";
                $pipes = array();
                $process = proc_open(
                    'phpunit' . $cliArguments . $testCases[$currentTestCase],
                    array(array('pipe', 'r'), array('pipe', 'w'), array('pipe', 'w')),
                    $pipes
                );

                if (is_resource($process)) {
                    echo str_repeat('=', 80), PHP_EOL;
                    echo "{$testCases[$currentTestCase]} test case started on instance $index.", PHP_EOL;

                    $worker['idle'] = false;
                    $worker['test_case'] = $currentTestCase;
                    $worker['process'] = $process;
                    $worker['process_start_time'] = time();
                    $worker['stdout'] = $pipes[1];
                    $worker['stderr'] = $pipes[2];
                    $instancesCount++;
                    $currentTestCase++;
                    sleep(1);
                } elseif (++$openProcessFailures > 1000) {
                    echo 'Could not open PHPUnit process for more than 1000 times.', PHP_EOL;
                    exit(1);
                }
                $index++;
            }
        } else {
            $status = proc_get_status($worker['process']);
            if (!$status['running']) {
                $cleanExitCode = $cleanExitCode && $status['exitcode'] === 0;
                echo "{$testCases[$worker['test_case']]} test case finished on instance {$index} ",
                "with exit code {$status['exitcode']}.", PHP_EOL;
                echo rtrim(stream_get_contents($worker['stdout']), PHP_EOL), PHP_EOL;
                file_put_contents('php://stderr', stream_get_contents($worker['stderr']));
                fclose($worker['stdout']);
                proc_close($worker['process']);

                if (!preg_match('/testsuite(.*)/', $testCases[$worker['test_case']], $matches)) {
                    echo "No /testsuite/ part in the path to test case {$testCases[$worker['test_case']]}.", PHP_EOL;
                    exit(1);
                }
                $testCaseId = str_replace(
                    array(DIRECTORY_SEPARATOR, '.php'),
                    array('_', ''),
                    ltrim($matches[1], DIRECTORY_SEPARATOR)
                );

                $testCaseOutputDir = $outputDir . DIRECTORY_SEPARATOR . $testCaseId . DIRECTORY_SEPARATOR;
                \Magento\Framework\Io\File::rmdirRecursive($testCaseOutputDir);
                mkdir($testCaseOutputDir);

                $testCaseLogsDir = str_replace('/', DIRECTORY_SEPARATOR, $worker['dir'] . "/var/logs-{$index}");
                rename($testCaseLogsDir, $testCaseOutputDir . 'logs');
                mkdir($testCaseLogsDir);

                $logFile = $testCaseOutputDir . 'logs' . DIRECTORY_SEPARATOR . 'logfile.xml';
                if ($junitLog && file_exists($logFile)) {
                    $resultXml = preg_replace('/<\?xml[^?]+\?>|<\/?testsuites>/', '', file_get_contents($logFile));
                    $resultXml = trim($resultXml);
                    fwrite($junitLog, $resultXml);
                }

                $worker['idle'] = true;
                $instancesCount--;
                $testCasesLeft--;
            } elseif (time() - $worker['process_start_time'] > $maxExecutionTime) {
                echo "{$testCases[$worker['test_case']]} test case on instance {$index} ",
                "exceeded execution time limit of {$maxExecutionTime} seconds and will be terminated.", PHP_EOL;
                $worker['process_start_time'] = PHP_INT_MAX; // terminate process only once
                proc_terminate($worker['process']);
            }
            $index++;
        }
    }
}

if ($junitLog) {
    fwrite($junitLog, '</testsuites>');
    fclose($junitLog);
}


if ($cleanExitCode) {
    echo 'Tests execution is completed successfully.', PHP_EOL;
} else {
    echo 'Tests execution is completed, but some child processes returned non-zero exit code.', PHP_EOL;
    exit(1);
}

/**
 * @param $testClassPath
 * @return bool
 */
function isTestClassAbstract($testClassPath)
{
    $classPath = str_replace('testsuite/', '', $testClassPath);
    $classPath = str_replace('.php', '', $classPath);
    $className = str_replace('/', '\\', $classPath);

    return (new ReflectionClass($className))->isAbstract();
}
