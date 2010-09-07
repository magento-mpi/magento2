<?php
/**
 * Abstract test class for Admin module
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_Abstract extends Test_Abstract
{
    /**
     * Helper local instance
     *
     * @var Helper_Admin
     */
    protected $_helper = null;

    /**
     * Initialize the environment
     */
    public function  setUp() {
        parent::setUp();

        // Get test parameters
        $this->_baseUrl = Core::getEnvConfig('backend/baseUrl');
        $this->_userName = Core::getEnvConfig('backend/auth/username');
        $this->_password = Core::getEnvConfig('backend/auth/password');
        $this->_siteName = Core::getEnvConfig('backend/managestores/site/name');
        $this->_siteCode = Core::getEnvConfig('backend/managestores/site/code');
        $this->_siteOrder = Core::getEnvConfig('backend/managestores/site/sortorder');
    }

    /**
     * Performs login into Admin
     * @return boolean
     */
    public function adminLogin($baseurl, $username, $password)
    {
        $result = true;
        $this->open($baseurl);
        $this->type($this->getUiElement("admin/pages/login/fields/username"), $username);
        $this->type($this->getUiElement("admin/pages/login/fields/password"), $password);
        $this->clickAndWait($this->getUiElement("admin/pages/login/buttons/loginbutton"));

        if ($this->isTextPresent($this->getUiElement("admin/pages/login/messages/invalidlogin"))) {
            $this->setVerificationErrors("Check 1 failed:: Invalid login name/passsword");
            $result = false;
        } else {
            if (!$this->waitForElement($this->getUiElement("admin/pages/login/images/mainlogo"), 30)) {
                $this->setVerificationErrors("Check 2 failed: Dashboard wasn't loaded");
                $result = false;
            }
        }
        return $result;
    }

      /**
     * Await appearing and disappearing "Please wait" gif-image
     *
     */
    public  function pleaseWait()
    {
        //
        // await for appear and disappear "Please wait" animated gif...
        for ($second = 0; ; $second++) {
            if ($second >= 60)  {
                break; //fail("timeout");
            }
            try {
                if (!$this->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) {
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }

        for ($second = 0; ; $second++) {
            if ($second >= 60)break;
            try {
                if ($this->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) {
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }
        sleep(1);
    }

    /**
     * Search row in the table satisfied condition Field "Key" == "value",
     * where "key" => "value" are parts of paramsArray
     * @param  $tableXPath
     * @param <type> $paramsArray
     * @return index of row, -1 if could not be founded
     * Note: this version works only for one(last) pair
     */
    public function getSpecificRow($tableXPath, $paramsArray) {
      Core::debug('getSpecificRow started');
      $colNum = $this->getXpathCount($tableXPath . "//tr[contains(@class,'heading')]//th");
      Core::debug('$colNum = ' . $colNum, 1);
      $rowNum = $this->getXpathCount($tableXPath . "//tbody//tr");
      Core::debug('$rowNum = ' . $rowNum, 1);

      foreach (array_keys($paramsArray) as $key) {
          //Open user with 'User Name' == name
          //Determine Column with 'User Name' title
          $keyColInd = -1;
          for ($col = 0; $col<= $colNum-1; $col++) {
            $cellLocator = $tableXPath . '.0.' . $col ;
            $cell = $this->getTable($cellLocator);
            if ( $key == $cell) {
                Core::debug($key . ' is founded in ' . $cellLocator, 7);
                $keyColInd = $col;
            }
            Core::debug($cellLocator . ' == ' . $cell);
          }
          if ($keyColInd == -1) {
              Core::debug($key . ' not founded in ' . $tableXPath . ' table', 7);
              return -1;
          } else {
              if ($keyColInd >-1) {
                  $bodyLocator =  $tableXPath . '//tbody';
                  $valueRowInd = -1;
                  for ($row = 0; $row <= $rowNum-1; $row++) {
                    $cellLocator = $bodyLocator . '.' . $row . '.' . $keyColInd;
                    $cell = $this->getTable($cellLocator);
                    if ($cell == $paramsArray[$key]) {
                        $valueRowInd = $row;
                        Core::debug('Founded in ' . $cellLocator,7);
                    }
                    Core::debug($cellLocator . ' == [' . $cell .']',7);
                  }
              }
          }
      }
      if ($valueRowInd > -1 ) {
        $valueRowInd++;
        return $valueRowInd;
       } else {
              Core::debug( $paramsArray[$key] . ' not founded in ' . $tableXPath . ' table', 7);
              return -1;
       }
    }
}

