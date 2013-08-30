<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Di\Compiler\Log\Writer;
use Magento\Tools\Di\Compiler\Log\Log;

class Console implements WriterInterface
{
    /**
     * Report messages by type
     * @var array
     */
    protected $_messages = array(
        Log::GENERATION_SUCCESS => 'Generated classes:',
        Log::GENERATION_ERROR => 'Errors during class generation:',
        Log::COMPILATION_ERROR => 'Errors during compilation:'
    );

    /**
     * Output log data
     *
     * @param array $data
     */
    public function write(array $data)
    {
        foreach ($data as $type => $classes) {
            echo $this->_messages[$type] . "\n";
            foreach ($classes as $className => $messages) {
                echo "\t" . $className . "\n";
                if (count($messages)) {
                    foreach ($messages as $message) {
                        if ($message) {
                            echo "\t\t - " . $message . "\n";
                        }
                    }
                }
            }
        }
    }
}
