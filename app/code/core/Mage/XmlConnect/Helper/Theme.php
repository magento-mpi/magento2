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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Helper_Theme extends Mage_Adminhtml_Helper_Data
{

    /**
     * Converts native Ajax data from flat to real array
     *
     * @param array $data   $_POST
     * @return array
     */
    public function convertPost($data)
    {
        $converted = array();
        $conf = array(
            'native' => array('navigationBar' => array(), 'body' => array(), 'categoryItem' => array(), 'itemActions'),
            'extra' => array('fontColors' => array())
        );
        foreach($data as $key => $val){
            $parts = explode('_', $key);
            $this->_parseUnderscoreArray($parts, $conf, $val);
        }
        $converted['conf'] = $conf;
        return $converted;
    }

    /**
     *  Convert 'conf_extra_tripple_something' to $conf['extra']['tripple']['something']
     *
     * @param string $parts
     * @param array &$conf
     * @param string $val
     * @return null
     */
    function _parseUnderscoreArray($parts, &$conf, $val)
    {
        // don't forget isset() checks
        list($key0,$key1,$key2,$key3) = $parts;
        if (!isset($conf[$key1])) {
            $conf[$key1] = array();
        }
        if (!isset($conf[$key1][$key2])) {
            $conf[$key1][$key2] = array();
        }
        $conf[$key1][$key2][$key3] = $val;
        $position3 = $val;
        return null;
    }

    public function getThemeFieldsArray()
    {

    }

    /**
     * Return for Color Themes Fields array.
     *
     *  @return array
     */
    public function getThemeAjaxParameters()
    {
        $themesArray = array (
            'conf_native_navigationBar_tintColor' => 'conf[native][navigationBar][tintColor]',
            'conf_native_body_primaryColor' => 'conf[native][body][primaryColor]',
            'conf_native_body_secondaryColor' => 'conf[native][body][secondaryColor]',
            'conf_native_categoryItem_backgroundColor' => 'conf[native][categoryItem][backgroundColor]',
            'conf_native_categoryItem_tintColor' => 'conf[native][categoryItem][tintColor]',

            'conf_extra_fontColors_header' => 'conf[extra][fontColors][header]',
            'conf_extra_fontColors_primary' => 'conf[extra][fontColors][primary]',
            'conf_extra_fontColors_secondary' => 'conf[extra][fontColors][secondary]',
            'conf_extra_fontColors_price' => 'conf[extra][fontColors][price]',

            'conf_native_body_backgroundColor' => 'conf[native][body][backgroundColor]',
            'conf_native_body_scrollBackgroundColor' => 'conf[native][body][scrollBackgroundColor]',
            'conf_native_itemActions_relatedProductBackgroundColor' => 'conf[native][itemActions][relatedProductBackgroundColor]'
        );
        return $themesArray;
    }

    /**
     * Returns JSON ready Themes array
     *
     * @params bool     $default    -    load defaults
     * @return array
     */
    public function getAllThemesArray($default = false)
    {
        $result = array();
        $themes = Mage::helper('xmlconnect/theme')->getAllThemes($default);
        foreach ($themes as $theme) {
            $result[$theme->getName()] = $theme->getFormData();
        }
        return $result;
    }

    /**
     *  Reads directory media/xmlconnect/themes/*
     *
     * @param  bool         $default - Reads default color Themes
     * @return array            - (of Mage_XmlConnect_Model_Theme)
     */
    public function getAllThemes()
    {
        $save_libxml_errors = libxml_use_internal_errors(TRUE);
        $result = array();
        $themeDir = Mage::getBaseDir('media') . DS . 'xmlconnect' . DS . 'themes';
        $d = opendir($themeDir);
        while (($f = readdir($d)) !== FALSE) {
            $f = $themeDir . DS . $f;
            if (is_file($f) && is_readable($f)) {
                try {
                    $result[] = Mage::getModel('xmlconnect/theme', $f);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        closedir($d);

        libxml_use_internal_errors($save_libxml_errors);
        usort($result, array('Mage_XmlConnect_Helper_Theme', 'sortThemes'));
        return $result;
    }

    /**
     *  Reads directory media/xmlconnect/themes/*
     *
     * @param  bool         $default - Reads default color Themes
     * @return array            - (of Mage_XmlConnect_Model_Theme)
     */
    public function resetAllThemes()
    {
        $save_libxml_errors = libxml_use_internal_errors(TRUE);
        $themeDir = Mage::getBaseDir('media') . DS . 'xmlconnect' . DS . 'themes';
        $defaultThemeDir = Mage::getBaseDir('media') . DS . 'xmlconnect' . DS . 'themes' . DS . 'default';
        $d = opendir($defaultThemeDir);
        while (($f = readdir($d)) !== FALSE) {
            $src = $defaultThemeDir . DS . $f;
            $dst = $themeDir . DS .$f;
            if (is_file($src) && is_readable($src) && is_writeable($themeDir)) {
                try {
                    if (!($result = copy($src, $dst))) {
                        Mage::throwException(Mage::helper('xmlconnect')->__('Can\t copy file "%s" to "%s".', $src, $dst));
                    } else {
                        $chmodResult = chmod($dst, 0777);
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        closedir($d);
        libxml_use_internal_errors($save_libxml_errors);
    }

    /**
     * Compare function, used in usort, so done as static
     *
     * @param Mage_XmlConnect_Model_Theme $a
     * @param Mage_XmlConnect_Model_Theme $b
     *
     * @return int
     */
    public static function sortThemes($a, $b)
    {
        if ($a->getName() == 'default') {
            return -1;
        }
        elseif ($b->getName() == 'default') {
            return 1;
        }
        else {
            return ($a->getName() < $b->getName()) ? -1: 1;
        }
    }

    public function savePost($name, $data)
    {
        $themes = self::getAllThemes();
        foreach ($themes as $theme) {
            if ($name == $theme->getName()) {
                $theme->importAndSaveData($data['conf']);
                break;
            }
        }
    }

    public function getCustomThemeName()
    {
        return 'custom';
    }

    public function getDefaultThemeName()
    {
        return 'default';
    }
}
