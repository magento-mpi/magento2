<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

use Magento\TestFramework\Helper\Bootstrap;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Assure that there are no redundant indexes declared in database
     */
    public function testDuplicateKeys()
    {
        if (!defined('PERCONA_TOOLKIT_BIN_DIR')) {
            $this->markTestSkipped('Path to Percona Toolkit is not specified.');
        }
        $checkerPath = PERCONA_TOOLKIT_BIN_DIR . '/pt-duplicate-key-checker';

        $dbConfig = Bootstrap::getInstance()->getBootstrap()->getDbConfig();
        $command = $checkerPath . ' -d ' . $dbConfig->dbName
            . ' h=' . $dbConfig->host . ',u=' . $dbConfig->username . ',p=' . $dbConfig->password;

        exec($command, $output, $exitCode);
        $this->assertEquals(0, $exitCode);
        $output = implode(PHP_EOL, $output);
        if (preg_match('/Total Duplicate Indexes\s+(\d+)/', $output, $matches)) {
            $this->fail($matches[1] . ' duplicate indexes found.' . PHP_EOL . PHP_EOL . $output);
        }
    }
}
