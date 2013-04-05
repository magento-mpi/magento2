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
class Core_Mage_StoreLauncher_Helper extends Mage_Selenium_AbstractHelper
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
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $tileButton
         */
        $tileButton = null;
        $tileButtons = array('open_drawer', 'edit_drawer');
        foreach($tileButtons as $key => $btnName) {
            if ($this->controlIsPresent('button', $btnName)) {
                $tileElement = $this->mouseOverDrawer($tile);
                $tileButton =
                    $this->getChildElements($tileElement, $this->_getControlXpath('button', $btnName), false);
                $tileButton = array_shift($tileButton);
                if ($tileButton->displayed()) {
                    $tileButton->click();
                    $this->waitForAjax();
                    $this->pleaseWait();
                    return (bool)$this->waitForElement(
                        $this->_getControlXpath(self::FIELD_TYPE_PAGEELEMENT, 'drawer_footer'));
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
            foreach($keys as $k => $v) {
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
        $connectionData = $this->_getDbCredentials();
        //Uncomment and change if Magento instance is not local
        /*$connectionData['dbname' ] = 'magento_db';
        $connectionData['username' ] = 'user';
        $connectionData['password' ] = '123123q';
        $connectionData['host' ] = '192.168.1.1';*/
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
}