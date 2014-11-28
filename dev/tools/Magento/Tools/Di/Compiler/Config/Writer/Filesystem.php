<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Di\Compiler\Config\Writer;

use Magento\Tools\Di\Compiler\Config\WriterInterface;

class Filesystem implements WriterInterface
{
    /**
     * @var array
     */
    protected $assertions = [
        'adminhtml.ser' => 'aec267c5ce723015db6912f08eaf1570',
        'global.ser' => '6f5a036a6451c3353acfda09aba95f58',
        'doc.ser' => '0830e4bd4780ddbaf87ae16fb087ca4a',
        'frontend.ser' => 'ef13cdf6dd8d4fc4507af863341f2c55',
        'webapi_rest.ser' => 'a4677a6f28548a5ea32a9ab8b89961b7',
        'webapi_soap.ser' => 'a1fd27e4ef715d5f090a3074c994c33b'
    ];

    /**
     * Writes config in storage
     *
     * @param string $areaCode
     * @param array $config
     * @return void
     */
    public function write($areaCode, array $config)
    {
        $this->initialize();
        foreach ($config['arguments'] as $key => $value) {
            if ($value !== null) {
                $config['arguments'][$key] = serialize($value);
            }
        }

        $serialized = serialize($config);
        file_put_contents(BP . '/var/di/' . $areaCode . '.ser', $serialized);
        $this->assertMd5($areaCode . '.ser');
    }

    /**
     * Initializes writer
     *
     * @return void
     */
    private function initialize()
    {
        if (!file_exists(BP . '/var/di')) {
            mkdir(BP . '/var/di');
        }
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    private function assertMd5($fileName)
    {
        echo ($this->assertions[$fileName] ==
        md5_file(BP . '/var/di/' . $fileName) ? '. ' : 'Failed for '. $fileName . ' ');
    }
}
