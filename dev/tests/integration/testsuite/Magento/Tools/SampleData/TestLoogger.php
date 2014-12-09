<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\SampleData;

use Magento\Setup\Model\LoggerInterface;

class TestLoogger implements LoggerInterface
{
    /**
     * Creates a test logger
     *
     * @return Logger
     */
    public static function factory()
    {
        $logger = new Logger;
        $logger->setSubject(new TestLoogger);
        return $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function log($message)
    {
        $this->writeLn($message);
    }

    /**
     * {@inheritdoc}
     */
    public function logError(\Exception $e)
    {
        $this->writeLn($e);
    }

    /**
     * {@inheritdoc}
     */
    public function logInline($message)
    {
        echo $message;
    }

    /**
     * {@inheritdoc}
     */
    public function logMeta($message)
    {
        $this->writeLn($message);
    }

    /**
     * {@inheritdoc}
     */
    public function logSuccess($message)
    {
        $this->writeLn($message);
    }

    /**
     * Write line
     *
     * @param string $message
     */
    private function writeLn($message)
    {
        echo $message . PHP_EOL;
    }
}
