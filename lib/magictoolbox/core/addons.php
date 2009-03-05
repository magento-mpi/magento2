<?php

    if(!function_exists('magicToolboxResizer')) {
        function magicToolboxResizer($product = null, $subdir = 'image', $s = null, $imageFile = null) {
            if($product == null) return false;

            $helper = Mage::helper('catalog/image')->init($product, $subdir, $imageFile);
            
            $model = Mage::getModel('catalog/product_image');
            $model->setDesctinationSubdir($subdir);
            if($imageFile == null) {
                $model->setBaseFile($product->getData($subdir));
            } else {
                $model->setBaseFile($imageFile);
            }
            
            $img = $helper->__toString();
            if($s == null) return $img;
            
            $size = getimagesize($model->getBaseFile());
            $w = $s;
            $h = round($s * $size[1] / $size[0]);
            if($h > $s) {
                $h = $s;
                $w = round($s * $size[0] / $size[1]);
            }

            $helper->resize($w, $h);
            $thumb = $helper->__toString();
            return array($img, $thumb);
        }
    }
    
?>