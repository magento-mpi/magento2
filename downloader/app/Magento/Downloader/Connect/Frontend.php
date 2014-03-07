<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloader\Connect;

/**
 * Class frontend
 *
 * @category   Magento
 * @package    Magento_Connect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Frontend extends \Magento\Connect\Frontend
{
    /**
     * Log stream or not
     *
     * @var string|null
     */
    protected $_logStream = null;

    /**
     * Output cache
     *
     * @var array
     */
    protected $_out = array();

    /**
     * Set log stream
     *
     * @param string|resource $stream 'stdout' or open php stream
     * @return $this
     */
    public function setLogStream($stream)
    {
        $this->_logStream = $stream;
        return $this;
    }

    /**
     * Retrieve log stream
     *
     * @return string
     */
    public function getLogStream()
    {
        return $this->_logStream;
    }

    /**
     * @param array $data
     * @return void
     */
    public function output($data)
    {

        $this->_out = $data;

        if ('stdout'===$this->_logStream) {
            if (is_string($data)) {
                echo $data."<br/>".str_repeat(" ", 256);
            } elseif (is_array($data)) {
                $data = array_pop($data);
                if (!empty($data['message']) && is_string($data['message'])) {
                    echo $data['message']."<br/>".str_repeat(" ", 256);
                } elseif (!empty($data['data'])) {
                    if (is_string($data['data'])) {
                        echo $data['data']."<br/>".str_repeat(" ", 256);
                    } else {
                        if (isset($data['title'])) {
                            echo $data['title']."<br/>".str_repeat(" ", 256);
                        }
                        if (is_array($data['data'])) {
                            foreach ($data['data'] as $row) {
                                foreach ($row as $msg) {
                                    echo "&nbsp;".$msg;
                                }
                                echo "<br/>".str_repeat(" ", 256);
                            }
                        } else {
                            echo "&nbsp;".$data['data'];
                        }
                    }
                }
            } else {
                print_r($data);
            }
        }
    }

    /**
     * Method for ask client about rewrite all files.
     *
     * @param string $string
     * @return void
     */
    public function confirm($string)
    {
        $formId = $_POST['form_id'];
        echo <<<SCRIPT
        <script type="text/javascript">
            if (confirm("{$string}")) {
                parent.document.getElementById('ignore_local_modification').value=1;
                parent.onSuccess();
                if (parent && parent.disableInputs) {
                    parent.disableInputs(false);
                }
                window.onload = function () {
                    parent.document.getElementById('{$formId}').submit();
                    parent.document.getElementById('ignore_local_modification').value='';
                }
            }
        </script>
SCRIPT;
    }

    /**
    * Retrieve output cache
    *
    * @return array
    */
    public function getOutput()
    {
        return $this->_out;
    }
}
