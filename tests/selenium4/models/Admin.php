<?php
/**
 * Admin framework model
 *
 * @author Magento Inc.
 */
class Model_Admin extends TestModelAbstract
{

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->baseUrl = Core::getEnvConfig('backend/baseUrl');
        $this->setBrowserUrl($this->baseUrl);
        $this->userName = Core::getEnvConfig('backend/auth/username');
        $this->password = Core::getEnvConfig('backend/auth/password');
    }

    /**
     * Performs login into the BackEnd
     *
     */
    public function doLogin($userName = null, $password = null) {
        $userName = $userName ? $userName : $this->userName;
        $password = $password ? $password : $this->password;

        $this->open($this->baseUrl);
        $this->waitForPageToLoad("10000");

        $this->setUiNamespace('admin/pages/login');

        $this->type($this->getUiElement("fields/username"), $userName);
        $this->type($this->getUiElement("fields/password"), $password);
        $this->clickAndWait($this->getUiElement("buttons/loginbutton"));

        if ($this->isTextPresent($this->getUiElement("messages/invalidlogin"))) {
            $this->setVerificationErrors("Login check 1 failed: Invalid login name/passsword");
        }
        if (!$this->waitForElement($this->getUiElement("images/mainlogo"), 30)) {
            $this->setVerificationErrors("Check 1 failed: Dashboard hasn't loaded");
        }
    }

    /**
     * Await appearing "Please wait" gif-image and disappearing
     *
     */
    public  function pleaseWait()
    {
        $loadingMask = $this->getUiElement('/admin/elements/progress_bar');

        // await for appear and disappear "Please wait" animated gif...
        for ($second = 0; ; $second++) {
            if ($second >= 60)  {
                break; //fail("timeout");
            }
            try {
                if (!$this->isElementPresent($loadingMask)) {
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }

        for ($second = 0; ; $second++) {
            if ($second >= 60)break;
            try {
                if ($this->isElementPresent($loadingMask)) {
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
      $this->printDebug('getSpecificRow started');

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
                $this->printDebug($key . ' is founded in ' . $cellLocator);
                $keyColInd = $col;
            }
            $this->printDebug($cellLocator . ' == ' . $cell);
          }
          if ($keyColInd == -1) {
              $this->printDebug($key . ' not founded in ' . $tableXPath . ' table');
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
                        $this->printDebug('Founded in ' . $cellLocator);
                    }
                    $this->printDebug($cellLocator . ' == [' . $cell .']');
                  }
              }
          }
      }
      if ($valueRowInd > -1 ) {
        $valueRowInd++;
        return $valueRowInd;
       } else {
              $this->printDebug($paramsArray[$key] . ' not founded in ' . $tableXPath . ' table');
              return -1;
       }
    }
}

