<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Config\Updaters;

class Replacer
{
    /**
     * @var array
     */
    protected $files;

    /**
     * @var boolean
     */
    protected $output;

    /**
     * @param array $files
     * @param boolean $output
     */
    public function __construct(array $files, $output)
    {
        $this->files = $files;
        $this->output = $output;
    }

    public function process()
    {
        foreach ($this->files as $file) {
            $content = $this->replace($file);
            $this->output($file, $content);
        }
    }

    /**
     * @param string $file
     * @return string
     */
    protected function replace($file)
    {
        $content = file_get_contents($file);
        $content = $this->updateInterface($content);
        $content = $this->changeSignature($content);
        $content = $this->fixResult($content);

        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function updateInterface($content)
    {
        $content = str_replace('Magento\Core\Model\Store\ConfigInterface', 'Magento\App\Config\ScopeConfigInterface', $content);
        $content = str_replace('Magento\Core\Model\Store\Config', 'Magento\App\Config\ScopeConfigInterface', $content);
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function changeSignature($content)
    {
        $properties = [
            '->_storeConfig',
            '->_storeConfig',
            '->coreStoreConfig',
            '->storeConfig',
            '->_scopeConfig',
        ];
        $properties = implode('|', $properties);
        $regExp = '/(?<start>(' . $properties . ')(\n*\s*)->((getConfig|getConfigFlag)\(\n*\s*))(?<first>([^,)]+\)?))/iS';
        $content = preg_replace_callback(
            $regExp,
            function ($matches) {
                $subject = $matches['start']
                    . $matches['first']
                    . ', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE';
                $subject = str_replace('getConfigFlag', 'isSetFlag', $subject);
                $subject = str_replace('getConfig', 'getValue', $subject);
                return $subject;
            },
            $content
        );
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function fixResult($content)
    {
        $content = str_replace(
            '), \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE;',
            ', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);',
            $content
        );

        $content = preg_replace_callback(
            '/' . preg_quote('), \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE') . '\n/s',
            function ($matches) {
                return str_replace(
                    '), \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE',
                    ', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE)',
                    $matches[0]
                );
            },
            $content
        );
        $content = str_replace(
            '), \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE ',
            ', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE) ',
            $content
        );
        $content = str_replace(
            '), \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE) {',
            ', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE)) {',
            $content
        );

        return $content;
    }

    /**
     * @param string $file
     * @param string $content
     */
    protected function output($file, $content)
    {
        if ($this->output) {
            echo $content;
        } else {
            file_put_contents($file, $content);
        }
    }
}
