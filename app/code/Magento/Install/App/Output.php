<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Install\App;

class Output
{
    /**
     * @param array $data
     * @return int longest key length
     */
    protected function getLongestKey(array $data)
    {
        $longest = 0;
        $keys = array_keys($data);
        foreach($keys as $key) {
            if (strlen($key) > $longest) {
                $longest = strlen($key);
            }
        }
        return $longest;
    }

    /**
     * Make array keys aligned to the longest
     *
     * @param $data
     * @return array
     */
    public function alignKeys($data)
    {
        $formattedData = array();
        $length = $this->getLongestKey($data);
        foreach($data as $key => $value) {
            $formattedData[str_pad($key, $length, ' ', STR_PAD_RIGHT)] = $value;
        }
        return $formattedData;

    }

    /**
     * Process an array to $key => $value format
     * and adapt keys to pretty output
     *
     * @param array $rawData
     * @return array
     */
    public function prepareArray(array $rawData)
    {
        $keyValData = array();

        // transform data to key => value format
        foreach ($rawData as $item) {
            $keyValData[$item['value']] = $item['label'];
        }

        return $this->alignKeys($keyValData);
    }

    /**
     * Make output human readable
     *
     * @param $var
     */
    public function readableOutput($var)
    {
        switch(true) {
            case is_array($var):
                $eol = '';
                foreach($var as $key => $value) {
                    if (is_array($value) || !is_scalar($value)) {
                        echo $eol . $key . ' => ' . var_export($value, true);
                    } else {
                        echo $eol . $key . ' -- ' . $value;
                    }
                    $eol = ',' . PHP_EOL;
                }
                echo PHP_EOL;
                break;
            case is_scalar($var):
                echo $var . PHP_EOL;
                break;
            default:
                var_export($var, true);
        }
    }

    /**
     * Display message
     *
     * @param string $message
     * @return void
     */
    public function success($message)
    {
        echo $message;
    }

    /**
     * Display error
     *
     * @param string $message
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function error($message)
    {
        echo $message;
        exit(1);
    }
}
