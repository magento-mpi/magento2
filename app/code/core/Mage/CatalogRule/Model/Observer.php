<?php

class Mage_CatalogRule_Model_Observer
{
    public function getFinalPrice($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $finalPrice = min($product->getFinalPrice(), $product->getRulePrice());
        $product->setFinalPrice($finalPrice);
    }
}