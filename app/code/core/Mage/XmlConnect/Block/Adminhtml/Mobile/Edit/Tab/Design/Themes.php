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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Themes extends Mage_Core_Block_Template
{
    protected function getAllThemes()
    {
        $result = array();
        foreach ($this->getThemes() as $theme) {
            $result[$theme->getName()] = $theme->getFormData();
        }
        return $result;
    }

    protected function addColorBox($id, $label, $data)
    {
        return array(
            'id'    => $id,
            'name'  => $id,
            'label' => $label,
            'value' => isset($data[$id]) ? $data[$id] : ''
        );
    }
    public function __construct()
    {
        parent::__construct();

        $model = Mage::registry('current_app');
        $data = $model->getFormData();

        $this->setColorFieldset (array (
            array ( 'id' => 'fieldColors', 'label' =>   $this->__('Color Themes'), 'fields' => array (
                $this->addColorBox('conf[native][navigationBar][tintColor]', $this->__('Header Background Color'), $data),
                $this->addColorBox('conf[native][body][primaryColor]', $this->__('Primary Color'), $data),
                $this->addColorBox('conf[native][body][secondaryColor]', $this->__('Secondary Color'), $data),
                $this->addColorBox('conf[native][categoryItem][backgroundColor]', $this->__('Category Item Background Color'), $data),
                $this->addColorBox('conf[native][categoryItem][tintColor]', $this->__('Category Button Color'), $data),
            )),
            array ( 'id' => 'fieldFonts', 'label' =>   $this->__('Fonts'), 'fields' => array (
                $this->addColorBox('conf[extra][fontColors][header]', $this->__('Header Font Color'), $data),
                $this->addColorBox('conf[extra][fontColors][primary]', $this->__('Primary Font Color'), $data),
                $this->addColorBox('conf[extra][fontColors][secondary]', $this->__('Secondary Font Color'), $data),
                $this->addColorBox('conf[extra][fontColors][price]', $this->__('Price Font Color'), $data),
            )),
            array ( 'id' => 'fieldAdvanced', 'label' =>  $this->__('Advanced Settings'), 'fields' => array (
                $this->addColorBox('conf[native][body][backgroundColor]', $this->__('Background Color'), $data),
                $this->addColorBox('conf[native][body][scrollBackgroundColor]', $this->__('Scroll Background Color'), $data),
                $this->addColorBox('conf[native][itemActions][relatedProductBackgroundColor]', $this->__('Related Product Background Color'), $data),
            )),
        ));
        $this->setTemplate('xmlconnect/themes.phtml');

    }

    protected function _prepareLayout()
    {

    }

    public function getSaveThemeActionUrl()
    {
        return $this->getUrl('*/*/saveTheme');
    }

   /**
    * Returns url for skin folder
    *
    * @param string $name  - file name
    *
    * @return string
    */
    protected function getImageUrl($name = '')
    {
        return  Mage::getDesign()->getSkinUrl(Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Abstract::XMLCONNECT_ADMIN_DEFAULT_IMAGES . $name);
    }
}
