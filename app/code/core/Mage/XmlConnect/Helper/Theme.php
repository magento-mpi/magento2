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

class Mage_XmlConnect_Helper_Theme extends Mage_Core_Helper_Abstract
{
    /**
     * Exports $this->_getUrl() function to public
     *
     * @param string $route
     * @param array $params
     *
     * @return array
     */
    public function getUrl($route, $params = array())
    {
        return $this->_getUrl($route, $params);
    }

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
        $conf['native']['navigationBar']['tintColor'] = isset($data['conf_native_navigationBar_tintColor']) ? $data['conf_native_navigationBar_tintColor'] : '';
        $conf['native']['body']['primaryColor'] = isset($data['conf_native_body_primaryColor']) ? $data['conf_native_body_primaryColor'] : '';
        $conf['native']['body']['secondaryColor'] = isset($data['conf_native_body_secondaryColor']) ? $data['conf_native_body_secondaryColor'] : '';
        $conf['native']['categoryItem']['backgroundColor'] = isset($data['conf_native_categoryItem_backgroundColor']) ? $data['conf_native_categoryItem_backgroundColor'] : '';
        $conf['native']['categoryItem']['tintColor'] = isset($data['conf_native_categoryItem_tintColor']) ? $data['conf_native_categoryItem_tintColor'] : '';

        $conf['extra']['fontColors']['header'] = isset($data['conf_extra_fontColors_header']) ? $data['conf_extra_fontColors_header'] : '';
        $conf['extra']['fontColors']['primary'] = isset($data['conf_extra_fontColors_primary']) ? $data['conf_extra_fontColors_primary'] : '';
        $conf['extra']['fontColors']['secondary'] = isset($data['conf_extra_fontColors_secondary']) ? $data['conf_extra_fontColors_secondary'] : '';
        $conf['extra']['fontColors']['price'] = isset($data['conf_extra_fontColors_price']) ? $data['conf_extra_fontColors_price'] : '';

        $conf['native']['body']['backgroundColor'] = isset($data['conf_native_body_backgroundColor']) ? $data['conf_native_body_backgroundColor'] : '';
        $conf['native']['body']['scrollBackgroundColor'] = isset($data['conf_native_body_scrollBackgroundColor']) ? $data['conf_native_body_scrollBackgroundColor'] : '';
        $conf['native']['itemActions']['relatedProductBackgroundColor'] = isset($data['conf_native_itemActions_relatedProductBackgroundColor']) ? $data['conf_native_itemActions_relatedProductBackgroundColor'] : '';

        $converted['conf'] = $conf;
        return $converted;
    }

    /**
     * Returns Color Themes JSON array.
     *
     *  @return string
     */
    function getThemeAjaxParameters()
    {
        $params = "
{
conf_native_navigationBar_tintColor                           : \$( 'conf[native][navigationBar][tintColor]').value,
conf_native_body_primaryColor                                 : \$( 'conf[native][body][primaryColor]').value,
conf_native_body_secondaryColor                               : \$( 'conf[native][body][secondaryColor]').value,
conf_native_categoryItem_backgroundColor                      : \$( 'conf[native][categoryItem][backgroundColor]').value,
conf_native_categoryItem_tintColor                            : \$( 'conf[native][categoryItem][tintColor]').value,

conf_extra_fontColors_header                                  : \$( 'conf[extra][fontColors][header]').value,
conf_extra_fontColors_primary                                 : \$( 'conf[extra][fontColors][primary]').value,
conf_extra_fontColors_secondary                               : \$( 'conf[extra][fontColors][secondary]').value,
conf_extra_fontColors_price                                   : \$( 'conf[extra][fontColors][price]').value,

conf_native_body_backgroundColor                              : \$( 'conf[native][body][backgroundColor]').value,
conf_native_body_scrollBackgroundColor                        : \$( 'conf[native][body][scrollBackgroundColor]').value,
conf_native_itemActions_relatedProductBackgroundColor         : \$( 'conf[native][itemActions][relatedProductBackgroundColor]').value
}
";
        return $params;
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
