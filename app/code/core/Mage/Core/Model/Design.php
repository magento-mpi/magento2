<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Design extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('core/design');
    }

    public function validate()
    {
        $this->getResource()->validate($this);
        return $this;
    }

    public function loadChange($storeId, $date = null)
    {
        $result = $this->getResource()
            ->loadChange($storeId, $date);

        if (count($result)){
            if (!empty($result['design'])) {
                $tmp = explode('/', $result['design']);
                $result['package'] = $tmp[0];
                $result['theme'] = $tmp[1];
            }

            $this->setData($result);
        }

        return $this;
    }

    /**
     * Clean cached js/css data
     *
     * @return  bool
     */
    public function cleanCache()
    {
        $mediaDir = Mage::getBaseDir('media');

        try {
            $directory['js'] = $mediaDir.DS.'js';
            $directory['css'] = $mediaDir.DS.'css';

            $io = new Varien_Io_File();

            foreach ($directory as $dir) {
                $io->cd($dir);

                foreach ($io->ls('files_only') as $cachedFile) {
                    $io->rm($cachedFile['text']);
                }
            }
        }
        catch (Exception $e) {
            Mage::printException($e);
        }

        return true;
    }
    /**
     * Returns the time the files were last modified
     *
     * @param array $items
     * @param string $dir
     * @return time
     */

    public function getCasheLastModified($items, $dir)
    {
        $lastModified = 0;

        foreach($items as $item) {
            $source = $dir.$item;

            if (file_exists($source)) {
                $lastModified = max($lastModified, filemtime($source));
            }
        }
        return $lastModified;
    }

    /**
     * Compile files
     *
     * @param array $items
     * @param string $dir
     * @param string $type
     * @return bool
     */

    public function compileCacheFiles($items, $dir, $type='js')
    {
        $recompile = true;

        $findDir = Mage::getBaseDir().DS.$dir;
        $baseDir = Mage::getBaseDir('media').DS.$type.DS;
        $basePath = Mage::getBaseUrl().$dir;

        $hashFile = md5(implode(',', $items)).'.'.$type;
        $cachedFile = $baseDir.$hashFile;

        if (file_exists($cachedFile)) {
            if(filemtime($cachedFile) > $this->getCasheLastModified($items, $findDir)){
                $recompile = false;
            }
        }

        if ($recompile) {
            $out = '';

            foreach($items as $source) {
                $file = $findDir.$source;
                if (file_exists($file)) {
                    $out .= file_get_contents($file) . "\n";
                }
            }

            if ($type == 'css') {
                $this->setBasePath($basePath);

                $cssImport = '/@import\\s+([\'"])(.*?)[\'"]/';
                $out = preg_replace_callback($cssImport, array($this, 'processCss'), $out);

                $cssUrl = '/url\\(\\s*([^\\)\\s]+)\\s*\\)/';
                $out = preg_replace_callback($cssUrl, array($this, 'processCss'), $out);
            }

           try {
               if (!is_dir($baseDir)){
                  if (mkdir($baseDir)) {
                     chmod($baseDir, 0777);
                  }
               }

               $fp = fopen($cachedFile, 'w+');
               fwrite($fp, $out);
               fclose($fp);
           }
           catch (Exception $e) {
               Mage::printException($e);
           }
        }

        return $hashFile;
    }

    /**
     * Process Css File
     *
     * @param array $match
     * @return string
     */
    public function processCss($match)
    {
        $import = ($match[0][0] == '@');

        if ($import) {
            $quote = $match[1];
            $uri = $match[2];
        } else {
            $quote = ($match[1][0] == "'" || $match[1][0] == '"') ? $match[1][0] : '';
            $uri = ($quote == '') ? $match[1] : substr($match[1], 1, strlen($match[1]) - 2);
        }

        if(substr($uri,0,3) == '../') {
            $uri = substr($uri,3);
        }

        $uri = $this->getBasePath().$uri;
        return $import ? "@import {$quote}{$uri}{$quote}" : "url({$quote}{$uri}{$quote})";
    }
}
