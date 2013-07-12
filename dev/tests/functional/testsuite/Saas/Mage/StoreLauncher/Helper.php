<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_StoreLauncher
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_StoreLauncher_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Possible tile states
     */
    public static $STATE_TODO = 0;
    public static $STATE_COMPLETE = 1;
    public static $STATE_DISMISSED = 2;
    public static $STATE_SKIPPED = 3;

    /**
     * Open Drawer popup
     *
     * @param string $tile Fieldset name from UIMap
     * @return bool
     */
    public function openDrawer($tile)
    {
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $tileButton */
        $tileButtons = array('open_drawer', 'edit_drawer');
        foreach ($tileButtons as $btnName) {
            if ($this->controlIsPresent('button', $btnName)) {
                $tileElement = $this->mouseOverDrawer($tile);
                $tileButton = $this->getChildElement($tileElement, $this->_getControlXpath('button', $btnName));
                if ($tileButton->displayed()) {
                    $tileButton->click();
                    $this->waitForAjax();
                    $this->pleaseWait();
                    $this->waitForControl(self::FIELD_TYPE_PAGEELEMENT, 'drawer_footer');
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Close Drawer popup
     *
     * @return bool
     */
    public function closeDrawer()
    {
        $this->clickButton('close_drawer', false);
        $this->waitForControlNotVisible(self::UIMAP_TYPE_FIELDSET, 'common_drawer');
        return true;
    }

    /**
     * Save Drawer
     *
     * @return bool
     */
    public function saveDrawer()
    {
        $this->clickButton('save_my_settings', false);
        $this->waitForAjax();
        $this->waitForControlNotVisible(self::UIMAP_TYPE_FIELDSET, 'common_drawer');
        return true;
    }

    /**
     * Move mouse over Tile
     *
     * @param $tile
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function mouseOverDrawer($tile)
    {
        $tileXpath = $this->_getControlXpath(self::UIMAP_TYPE_FIELDSET, $tile);
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $tileElement
         */
        $tileElement = $this->getElement($tileXpath);
        $this->moveto($tileElement);
        return $tileElement;
    }

    /**
     * Get Tile element background color
     *
     * @param $element
     * @return null|string
     */
    public function getTileBgColor($element)
    {
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $element
         */
        $elementId = $element->attribute('id');
        if ($elementId) {
            $script =
                "return window.getComputedStyle(document.getElementById(arguments[0])).backgroundColor;";
            $elementStyle = $this->execute(array('script' => $script, 'args' => array(0 => $elementId)));
            return $elementStyle;
        }
        return null;
    }

    protected function _getDbCredentials()
    {
        $data = array();
        $basePath = rtrim(SELENIUM_TESTS_BASEDIR, DIRECTORY_SEPARATOR)
                    . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
        $localXml = $basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'etc'
                    . DIRECTORY_SEPARATOR . 'local.xml';
        $keys = array('host', 'username', 'password', 'dbname');
        if (file_exists($localXml)) {
            $config = simplexml_load_file($localXml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $connection = $config->xpath('//connection');
            foreach ($keys as $v) {
                $data[$v] = (string)$connection[0]->$v;
            }
        }
        return $data;
    }

    /**
     * Change Tile State by direct DB query
     *
     * @param string $tileCode Correspond value from DB
     * @param int $tileState STATE_TODO|STATE_COMPLETE|STATE_DISMISSED|STATE_SKIPPED
     * @return bool
     */
    public function setTileState($tileCode, $tileState)
    {
        $connectionData = $this->tmtHelper()->getTenantDbCredentials();
        if (!empty($connectionData)) {
            try {
                $connection = new PDO(
                    'mysql:dbname=' . $connectionData['dbname' ] . ';host=' . $connectionData['host'],
                    $connectionData['username' ],
                    $connectionData['password' ]);
                $sql = "UPDATE launcher_tile SET state=? WHERE tile_code=?";
                $q = $connection->prepare($sql);
                $q->execute(array($tileState, $tileCode));
                return true;
            } catch (PDOException $e) {
                $this->fail($e->getMessage());
            }
        }
        return $this->fail('Could not set Tile state');
    }

    /**
     * Reset Payments tile state
     */
    public function resetPaymentsTile()
    {
        if ($this->isTileComlete('payment_tile')) {
            $this->navigate('system_configuration');
            $this->systemConfigurationHelper()->configure('PaymentMethod/paypal_disable');
            $this->systemConfigurationHelper()->configure('PaymentMethod/authorize_net_disable');
            $this->admin();
            $this->storeLauncherHelper()->openDrawer('payment_tile');
            $this->storeLauncherHelper()->saveDrawer();
        }
    }

    /**
     * Reset Product tile state
     */
    public function resetProductTile()
    {
        if ($this->isTileComlete('product_tile')) {
            //Remove all products
            $this->navigate('manage_products');
            $this->runMassAction('Delete', 'all');
            $this->storeLauncherHelper()->setTileState('product', Saas_Mage_StoreLauncher_Helper::$STATE_TODO);
        }
    }

    /**
     * Reset Shipping tile state
     */
    public function resetShippingTile()
    {
        if ($this->isTileComlete('shipping_tile')) {
            $this->navigate('system_configuration');
            $this->systemConfigurationHelper()->configure('ShippingMethod/shipping_disable');
            $this->admin();
            $this->storeLauncherHelper()->openDrawer('shipping_tile');
            $this->clickControl('pageelement', 'shipping_switcher', false);
            $this->storeLauncherHelper()->saveDrawer();
        }
    }

    /**
     * Reset StoreInfo tile state
     */
    public function resetStoreInfoTile()
    {
        if ($this->isTileComlete('bussines_info_tile')) {
            $this->navigate('system_configuration');
            $this->systemConfigurationHelper()->configure('ShippingSettings/store_information_empty');
            $this->systemConfigurationHelper()->configure('General/general_default_emails');
            $this->admin();
        }
    }

    /**
     * Reset StoreInfo tile state
     */
    public function resetTaxTile()
    {
        if ($this->isTileComlete('tax_rules_tile')) {
            $this->storeLauncherHelper()->setTileState('tax', Saas_Mage_StoreLauncher_Helper::$STATE_TODO);
        }
    }

    /**
     * Get tile state
     * @param $tileCode
     *
     * @return bool
     */
    protected function isTileComlete($tileCode)
    {
        $tileState = $this->getControlAttribute(self::UIMAP_TYPE_FIELDSET, $tileCode, 'class');
        return (strpos($tileState, 'tile-complete') !== false) ? true : false;
    }
}