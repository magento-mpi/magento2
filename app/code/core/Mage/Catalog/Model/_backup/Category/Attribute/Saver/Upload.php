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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * category upload saver model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Category_Attribute_Saver_Upload extends Mage_Catalog_Model_Category_Attribute_Saver
{
    public function save($categoryId, $value)
    {
        if ($value = $this->_uploadFile()) {
            parent::save($categoryId, $value);
        }
        return $this;
    }
    
    protected function _uploadFile()
    {
        $uploadFile = new Varien_File_Uploader($this->_attribute->getFormFieldName());
        $uploadFile->setFilesDispersion(true);
        $uploadFile->setAllowRenameFiles(true);
        $uploadFile->save(Mage::getBaseDir('upload'));
        return $uploadFile->getUploadedFileName();
    }
}
