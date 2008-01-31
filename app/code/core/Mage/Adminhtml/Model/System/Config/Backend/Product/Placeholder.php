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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config category field backend
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Vitaliy Korotun <vitaliy.korotun@varien.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Product_Placeholder
{
    public function afterSave(Varien_Object $configData)
    {
        $value     = $configData->getValue();

        if (is_array($value) && !empty($value['delete'])) {
            $configData->setValue('');
        }

        if ($_FILES['groups']['tmp_name'][$configData->getGroupId()]['fields'][$configData->getField()]['value']){
            try {
                $file['tmp_name'] = $_FILES['groups']['tmp_name'][$configData->getGroupId()]['fields'][$configData->getField()]['value'];
                $file['name'] = $_FILES['groups']['name'][$configData->getGroupId()]['fields'][$configData->getField()]['value'];
                $uploader = new Varien_File_Uploader($file);
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                $uploader->setAllowRenameFiles(true);
                $uploader->save(Mage::getStoreConfig('system/filesystem/media').'/catalog/product/placeholder');
            }
            catch (Exception $e){
                return $this;
            }

            if ($fileName = $uploader->getUploadedFileName()) {
                $fileName = Mage::getBaseUrl('media').'catalog/product/placeholder/'.$fileName;
                $configData->setValue($fileName);
            }
        }
        return $this;
    }
}