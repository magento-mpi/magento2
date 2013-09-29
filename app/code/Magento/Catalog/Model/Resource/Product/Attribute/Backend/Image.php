<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product image attribute backend
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Product\Attribute\Backend;

class Image
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * After save
     *
     * @param \Magento\Object $object
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Image
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }

        try {
            $uploader = new \Magento\Core\Model\File\Uploader($this->getAttribute()->getName());
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
        } catch (\Exception $e){
            return $this;
        }
        $uploader->save(\Mage::getBaseDir('media') . '/catalog/product');

        $fileName = $uploader->getUploadedFileName();
        if ($fileName) {
            $object->setData($this->getAttribute()->getName(), $fileName);
            $this->getAttribute()->getEntity()
                 ->saveAttribute($object, $this->getAttribute()->getName());
        }
        return $this;
    }
}
