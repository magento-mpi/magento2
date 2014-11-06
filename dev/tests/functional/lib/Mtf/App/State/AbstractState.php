<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\App\State;

/**
 * Abstract class AbstractState
 *
 */
abstract class AbstractState implements StateInterface
{
    /**
     * Specifies whether to clean instance under test
     *
     * @var bool
     */
    protected $isCleanInstance = false;

    /**
     * @inheritdoc
     */
    public function apply()
    {
        if ($this->isCleanInstance) {
            $this->clearInstance();
        }
    }

    /**
     * Clear Magento instance: remove all tables in DB and use dump to load new ones, clear Magento cache
     *
     * @throws \Exception
     */
    public function clearInstance()
    {
        $magentoBaseDir = dirname(dirname(dirname(MTF_BP)));
        $config = simplexml_load_file($magentoBaseDir . '/app/etc/local.xml');

        $host = (string)$config->connection->host;
        $user = (string)$config->connection->username;
        $password = (string)$config->connection->password;
        $database = (string)$config->connection->dbname;

        $fileName = MTF_BP . '/' . $database . '.sql';
        if (!file_exists($fileName)) {
            echo('Database dump was not found by path: ' . $fileName);
            return;
        }

        // Drop all tables in database
        $mysqli = new \mysqli($host, $user, $password, $database);
        $mysqli->query('SET foreign_key_checks = 0');
        if ($result = $mysqli->query("SHOW TABLES")) {
            while ($row = $result->fetch_row()) {
                $mysqli->query('DROP TABLE ' . $row[0]);
            }
        }
        $mysqli->query('SET foreign_key_checks = 1');
        $mysqli->close();

        // Load database dump
        exec("mysql -u{$user} -p{$password} {$database} < {$fileName}", $output, $result);
        if ($result) {
            throw new \Exception('Database dump loading has been failed: ' . $output);
        }

        // Clear cache
        exec("rm -rf {$magentoBaseDir}/var/*", $output, $result);
        if ($result) {
            throw new \Exception('Cleaning Magento cache has been failed: ' . $output);
        }
    }
}
