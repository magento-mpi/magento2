<?php
/**
 * Frontend_product model
 *
 * @author Magento Inc.
 */
class Model_Frontend_Product extends Model_Frontend
{

    /**
     * Product type constants
     */
    const SIMPLE   = 1;
    const GROUPED = 2;
    const CONFIGURABLE  = 3;
    const BUNDLE = 4;
    const VIRTUAL = 5;
    const GIFTCARD = 6;
    const DOWNLODABLE = 7;

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $params = Core::getEnvConfig('backend/create_product');
        $params ['subcategoryname'] = Core::getEnvConfig('backend/manage_categories/subcategoryname');

        $this->productData = $params;

        $this->categoryModel = $this->getModel('frontend/category');
    }

    /*
     * Open product page from category page
     * @params array
     * return boolean
     */
    public function  doOpen($params = array())
    {
        $this->printDebug('doOpenProduct started...');
        $productData = $params ? $params : $this->productData;
//        print_r($productData);
        $productName = $productData['productName'];
//        $sku = $productData['sku'];
//        $categoryName = $productData['subcategoryname'];

        if ($this->categoryModel->doOpen ($productData)) {
            if ($this->waitForElement($this->getUiElement("/frontend/pages/category/links/productName",$productName),5)) {
                //Move to ProductPage
                $this->clickAndWait($this->getUiElement("/frontend/pages/category/links/productName",$productName));
            } else {
                    $this->printInfo('doOpenProduct: "' . $productName . '" product page could not be opened');
                    return false;
            }
                $this->printInfo('doOpenProduct: "' . $productName . '" product page has been opened');
            return true;
        }

     }

    /*
     * Tries to determine type of opened product.
     * Default value is SIMPLE(VIRTUAL)
     */
    public function detectType()
    {
        $this->printDebug('detectType() started');
        $result = self::SIMPLE;

        $type_markers = $this->getUiElement('frontend/pages/product/elements/types');

        if ($this->isElementPresent($type_markers['grouped'])) {
            $result = self::GROUPED;
        }   elseif ($this->isElementPresent($type_markers['downlodable'])) {
                $result = self::DOWNLODABLE;
            } elseif ($this->isElementPresent($type_markers['giftcard'])) {
                $result = self::GIFTCARD;
                } elseif ($this->isElementPresent($type_markers['configurable'])) {
                        $result = self::CONFIGURABLE;
                    }   elseif ($this->isElementPresent($type_markers['bundle'])) {
                            $result = self::BUNDLE;
                        }

        $this->printDebug('detectType() finished: ' . $result);
        return $result;
    }

    /*
     * Place opened product to shopping cart.
     * Supports SIMPLE, VIRTUAL, GROUPED product types
     * next entries expected in $params array :
     *  'baseUrl',
        'categoryName' => 'SL-Category/Base',
        'productName' => 'Grouped Product - Base',
        'associatedProducts' => array (
                                    'productName' => Qty,
                                    'productName' => Qty,
                                    )
     *
     */
    public function placeToCart($params = array())
    {
        $result = true;
        $this->printDebug('placeToCart() started...');
        $productType = $this->detectType();
        switch ($productType):
            case self::SIMPLE:
                    $this->type($this->getUiElement('/frontend/pages/product/inputs/qty'),$params['qty']);
                    break;
            case self::GROUPED:
                $associatedProducts = $params['associatedProducts'];
                foreach ($associatedProducts as $key => $value) {
                    $this->printDebug($key . ' ->' . $value);
                    $qty_input = $this->getUiElement('/frontend/pages/product/inputs/grouped_qty',$key);
                    if (!$this->waitForElement($qty_input,1)) {
                        $this->setVerificationErrors("Check 2: Grouped Product " . $key . " Qty input box could not be located");
                        $result = false;
                    }
                    $this->type($qty_input,$value);
                }
                break;
            case self::CONFIGURABLE:
                $configOptions = $params['configOptions'];
                foreach ($configOptions as $key => $value) {
                    $this->printDebug($key . ' ->' . $value);
                    $option_selector = $this->getUiElement('/frontend/pages/product/inputs/configurable_option',$key);
                    if (!$this->waitForElement($option_selector,1)) {
                        $this->setVerificationErrors("Check 3: Configurable Product " . $key . " option dropdown box could not be located");
                        $result = false;
                    }
                    $this->select($option_selector . '//select',$value);
                }
                break;
                ;
            case self::BUNDLE:
                $bundleOptions = $params['bundleOptions'];
                foreach ($bundleOptions as $key => $value) {
                    $this->printDebug($key . ' ->' . $value);
                    $option_type = $this->detectOptionType($key);
                    $this->printDebug('option_type = ' . $option_type);
                    if (($option_type == 'checkbox') or ($option_type == 'radio')) {
                        $option_param  = array (
                            $key,
                            $value
                        );
                        $this->click($this->getUiElement('/frontend/pages/product/inputs/bundle_option_value_selection', $option_param) . '//input');
                    }

                    if ($option_type == 'select') {
                        $option_param  = array (
                            $key,
                            $value
                        );
//                        $this->printDebug('Try to select in selector: ' . $this->getUiElement('/frontend/pages/product/inputs/bundle_option_value', $option_param));
                        $selection = 'label=glob:' . $value . '*';
//                        $this->printDebug('select option: ' . $selection );
                        $this->select($this->getUiElement('/frontend/pages/product/inputs/bundle_option_value', $option_param) . '//select',  $selection);
                    }
                 }
                break;
                ;
            case self::DOWNLODABLE:
                $downloadOptions = $params['downloadOptions'];
                foreach ($downloadOptions as $key => $value) {
                    $this->printDebug($key . ' ->' . $value);
                    $locator = $this->getUiElement('/frontend/pages/product/inputs/download_option_value', $value);                    
                    if ($this->waitForElement($locator,3)) {
                        $this->printDebug('click to :' . $locator);
                        $this->click($locator);
                    } else {
                        $this->printInfo('Option ' . $value . ' not founded in product page');
                    }
                    
                 }
                break;
                ;
            case self::GIFTCARD:
                $this->type($this->getUiElement('/frontend/pages/product/inputs/gc_sender_name'),$params['senderName']);
                $this->type($this->getUiElement('/frontend/pages/product/inputs/gc_sender_email'),$params['senderEmail']);
                $this->type($this->getUiElement('/frontend/pages/product/inputs/gc_recipient_name'),$params['recipientName']);
                $this->type($this->getUiElement('/frontend/pages/product/inputs/gc_recipient_email'),$params['recipientEmail']);
                $this->type($this->getUiElement('/frontend/pages/product/inputs/gc_message'),$params['message']);
                break;
                ;

        endswitch;

        $this->clickAndWait($this->getUiElement('/frontend/pages/product/buttons/addToCart'));

        // Check for success message
        if ($this->waitForElement($this->getUiElement('/frontend/pages/shopping_cart/messages/added',$params["productName"]),1)) {
            $this->printInfo($params["productName"]. ' has been added to Shopping Cart');
        } else {
            $this->setVerificationErrors('placeToCart: No success message');
            $result = false;
        };

        //Check for presence and qty
        if (($productType == self::SIMPLE) || ($productType == self::VIRTUAL)) {
            //Check for product presense in the list
            if (!$this->waitForElement($this->getUiElement('/frontend/pages/shopping_cart/elements/shoping_cart_item',$params["productName"]),1)) {
                $this->setVerificationErrors("Check 2: Product " . $params['productName'] . " doesn't appeared in the shopping cart list");
                $result = false;
            }
        }
        if (($productType == self::GROUPED)) {
            $associatedProducts = $params['associatedProducts'];
            foreach ($associatedProducts as $key => $value) {
                $qty_input = $this->getUiElement('/frontend/pages/shopping_cart/inputs/item_qty',$key);
                //Check for product presense in the list
                if (!$this->waitForElement($this->getUiElement('/frontend/pages/shopping_cart/elements/shoping_cart_item',$key),1)) {
                    $this->setVerificationErrors("Check 2: Product " . $key . " doesn't appeared in the shopping cart list");
                    $result = false;
                } elseif ($this->waitForElement($qty_input,1)) {
                    //Check for product qty in the list                    
                    if ($this->getValue($qty_input) != $value) {
                        $this->setVerificationErrors("Check 3: Product " . $key . " qty=[" . $this->getValue($qty_input) . "] in the shopping cart does not macthed to " . $value);
                        $result = false;
                    }                               
                }
            }
        }
        $this->printDebug('placeToCart() finished with ' . $result);
        return $result;
    }

    /**
     * Detect type of bundle (custom) option
     * @param - $optionName - title of option
     * @return one from  'input', 'select', 'checkbox' or -1 on fail
     */
    public function detectOptionType($optionName = null)
    {
        $result = -1;
        $this->printDebug('detectOptionType() started...');
        if (isset($optionName)) {
            $locator = $this->getUiElement('/frontend/pages/product/inputs/bundle_option',$optionName);
            if ($this->getXpathCount($locator)) {
//                $this->printDebug($optionName . ' has been founded');
                $options_locator = $this->getUiElement('/frontend/pages/product/inputs/bundle_option_value',$optionName);
                if ($this->getXpathCount($options_locator)) {
//                    $this->printDebug('Options has been founded');
                    if ($this->getXpathCount($options_locator . '//li[1]/input')>0) {
                        // have inputs
                        $result = $this->getAttribute($options_locator . '//li[1]/input/@type');
                    };
                    if ($this->getXpathCount($options_locator . '//select')>0) {
                        // have selectors
                        $result = 'select';
                    };                    
                }
            } else {
                $this->printDebug('Option ' . $optionName . ' not founded in the page');
            }
            
        }
        $this->printDebug('Option ' . $optionName . ' with type = ' . $result . ' has founded in the page');
        $this->printDebug('detectOptionType() finished');
        return $result;
    }


    // Smoke TestCase

    /**
     * Test correcteness of appearing $product category page.
     * Checks:
     *  1 - Product Image element
     *  2 - productName on breadcrumbs
     *  3 - productName on product-name section
     *  4 - PriceOnPage matched to config value
     *  5 - product page could be opened
     * @param name - product Name
     * @param subcategoryname - category name
     * @return boolean
     */
    public function testProduct($params = array())
    {
        $productData = $params ? $params : $this->productData;
        $categoryName = $productData['subcategoryname'];
        $productName =  $productData['name'];
        $price = $productData['price'];
        $this->printDebug("testProduct($productName) started...");

        $result = true;
        if ($this->doOpen($productData)) {
            // Do some checks:
            // Check for presence of Product Image
            if (!$this->waitForElement($this->getUiElement("frontend/pages/product/elements/productImage"),2)) {
                $this->setVerificationErrors('Check 1: No Product Image on page has been founded');
                $result = false;
            }

            // Check for presence product name on breadcrumbs
            if (!$this->waitForElement($this->getUiElement("frontend/pages/product/elements/breadcrumb",$productName),2)) {
                $this->setVerificationErrors('Check 2: No productName on breadcrumbs has been founded');
                $result = false;
            }

            // Check for presence product name on product-page
            if (!$this->waitForElement($this->getUiElement("frontend/pages/product/elements/name",$productName),2)) {
                $this->setVerificationErrors('Check 3: No productName on product-name section has been founded');
                $result = false;
            }

            // Check for presence correct price on product-page
            if ($this->waitForElement($this->getUiElement("frontend/pages/product/elements/price"),2)) {
                $priceOnPage = $this->getText($this->getUiElement("frontend/pages/product/elements/price"));
                $priceFromConfig = $this->money_format('%.2n',$price);
                if ($priceOnPage != $priceFromConfig) {
                    $this->setVerificationErrors('Check 4: PriceOnPage [' . $priceOnPage . '] did not matched to expected [' . $priceFromConfig . ']');
                    $result = false;
                }
            } else {
                $this->setVerificationErrors('Check 5: No price-box has been founded');
                $result = false;
            }

        } else {
            $this->setVerificationErrors("Check 6: Product $productName  page could not be opened");
            $result = false;
        }
        return $result;
    }

}
