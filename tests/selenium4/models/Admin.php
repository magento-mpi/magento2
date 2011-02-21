<?php

/**
 * Admin framework model
 *
 * @author Magento Inc.
 */
class Model_Admin extends TestModelAbstract {

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
    public function doLogin($userName = null, $password = null)
    {
        $result = true;
        $userName = $userName ? $userName : $this->userName;
        $password = $password ? $password : $this->password;

        $this->open($this->baseUrl);
        $this->waitForPageToLoad("20000");

        $this->setUiNamespace('admin/pages/login');

        $this->type($this->getUiElement("fields/username"), $userName);
        $this->type($this->getUiElement("fields/password"), $password);
        $this->clickAndWait($this->getUiElement("buttons/loginbutton"));

        if ($this->isTextPresent($this->getUiElement("messages/invalidlogin"))) {
            $this->setVerificationErrors("Login check 1 failed: Invalid login name/passsword");
            $result = false;
        }
        if (!$this->waitForElement($this->getUiElement("images/mainlogo"), 30)) {
            $this->setVerificationErrors("Check 1 failed: Dashboard hasn't loaded");
            $result = false;
        }
        if ($result) {
            $this->printInfo('Logged to Admin');
        }
        return $result;
    }

    /**
     * Await appearing "Please wait" gif-image and disappearing
     *
     */
    public function pleaseWait()
    {
        $loadingMask = $this->getUiElement('/admin/global/elements/progress_bar');

        // await for appear and disappear "Please wait" animated gif...
        for ($second = 0;; $second++) {
            if ($second >= 60) {
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

        for ($second = 0;; $second++) {
            if ($second >= 60

                )break;
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
    public function getSpecificRow($tableXPath, $paramsArray)
    {
        $this->printDebug('getSpecificRow started');

        $colNum = $this->getXpathCount($tableXPath . "//tr[contains(@class,'heading')]//th");
        $this->printDebug('$colNum = ' . $colNum);
        $rowNum = $this->getXpathCount($tableXPath . "//tbody//tr");
        $this->printDebug('$rowNum = ' . $rowNum);

        foreach (array_keys($paramsArray) as $key) {
            //Open user with 'User Name' == name
            //Determine Column with 'User Name' title
            $keyColInd = -1;
            for ($col = 0; $col <= $colNum - 1; $col++) {
                $cellLocator = $tableXPath . '.0.' . $col;
                $cell = $this->getTable($cellLocator);
                if ($key == $cell) {
                    $this->printDebug($key . ' is founded in ' . $cellLocator);
                    $keyColInd = $col;
                }
                $this->printDebug($cellLocator . ' == ' . $cell);
            }
            if ($keyColInd == -1) {
                $this->printDebug($key . ' not founded in ' . $tableXPath . ' table');
                return -1;
            } else {
                if ($keyColInd > -1) {
                    $bodyLocator = $tableXPath . '//tbody';
                    $valueRowInd = -1;
                    for ($row = 0; $row <= $rowNum - 1; $row++) {
                        $cellLocator = $bodyLocator . '.' . $row . '.' . $keyColInd;
                        $cell = $this->getTable($cellLocator);
                        if ($cell == $paramsArray[$key]) {
                            $valueRowInd = $row;
                            $this->printDebug('Founded in ' . $cellLocator);
                        }
                        $this->printDebug($cellLocator . ' == [' . $cell . ']');
                    }
                }
            }
        }
        if ($valueRowInd > -1) {
            $valueRowInd++;
            return $valueRowInd;
        } else {
            $this->printDebug($paramsArray[$key] . ' not founded in ' . $tableXPath . ' table');
            return -1;
        }
    }

    /**
     * Verify is there a value for the field and select it.
     * It is necessary to set UiNamespace before using a function
     * @param <type> $field
     * @param <type> $value
     * $field path - "UiNamespace"/selectors/"$field"
     * @param <type> $params
     * @return boolean
     */
    public function checkAndSelectField($params, $field)
    {
        $result = true;
        if ($this->isSetValue($params, $field) != NULL) {
            $value = $this->isSetValue($params, $field);
            if ($this->isElementPresent($this->getUiElement("selectors/" . $field) .
                            $this->getUiElement("/admin/global/elements/option_for_field", $value))) {
                $this->select($this->getUiElement("selectors/" . $field), "label=" . $value);
            } else {
                $this->printInfo("The value '" . $value . "' cannot be set for the field '" . $field . "'");
                $result = false;
            }
        } 
        return $result;
    }

    /**
     * Verify is there a value for the field and fill it
     * It is necessary to set UiNamespace before using a function
     * @param <type> $params
     * @param <type> $field
     * $field path - "UiNamespace"/inputs/"$field"     *
     * @param <type> $number
     * If $field is raised through Xpath expression such as //*[@id="selector_%s"] than %s=$number
     * It is necessary to set NULL if the $number is not needed
     * @return boolean
     */
    public function checkAndFillField($params, $field, $number)
    {
        if ($this->isSetValue($params, $field) != NULL) {
            $value = $this->isSetValue($params, $field);
            $this->type($this->getUiElement("inputs/" . $field, $number), $value);
        }
    }

    /**
     * Search element(s) and perform action on them.
     * It is necessary to set UiNamespace before using a function.
     * @param string $tableContainer
     * path variable is set on the basis of such a principle: => XPath: UiNamespace/elements/$tableContainer
     * @param array $searchElements
     * ARRAY which contains search criteria and their values.
     * Example $searchElements: ar = array('searchBy1'=>'value1','searchBy2'=>'value2','searchBy2'=>'value2');,
     * where 'searchBy1', 'searchBy2', .. path to XPath field, for which the value 'value1' is set.
     * This function works only with field types 'input'.
     * Principle of task 'searchBy1' => XPath: UiNamespace/inputs/'searchBy1'
     * @param string $action
     * $action can have only 2 values: "mark"(mark the found element) and "open"(Open the found element)
     * @param <type> $tableNumber
     * If $tableContainer is raised through Xpath expression such as //*[@id="table_%s"] then %s=$tableNumber
     * When calling a function it is necessary to set NULL if the $tableNumber is not needed.
     * @return boolean
     */
    public function searchAndDoAction($tableContainer, $searchElements, $action, $tableNumber)
    {
        $result = TRUE;
        if (is_array($searchElements) and count($searchElements) > 0) {
            foreach ($searchElements as $key => $value) {
                $this->type($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                        $this->getUiElement("/admin/global/inputs/" . $key), $value);
            }
            $this->click($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                    $this->getUiElement("/admin/global/buttons/search"));
            if ($this->isTextPresent('Please wait...')) {
                $this->pleaseWait();
            } else {
                $this->waitForPageToLoad("30000");
            }
            if ($this->isTextPresent($this->getUiElement('/admin/global/elements/no_records'))) {
                $this->printInfo("\r\n Element not found.");
                $result = FALSE;
            } elseif ($this->isElementPresent($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                            $this->getUiElement('/admin/global/elements/filtered_element', $searchElements))) {
                if ($action == "mark") {
                    $this->click($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                            $this->getUiElement('/admin/global/elements/mark_filtered_element', $searchElements));
                } elseif ($action == "open") {
                    $this->click($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                            $this->getUiElement('/admin/global/elements/filtered_element', $searchElements));
                    for ($second = 0;; $second++) {
                        if ($second >= 60)
                            break;
                        try {
                            if (!$this->isElementPresent($this->getUiElement("elements/" . $tableContainer, $tableNumber)))
                                break;
                        } catch (Exception $e) {
                            
                        }
                        sleep(2);
                    }
                }
            } else {
                $this->printInfo(("\r\n Element not found."));
                $result = FALSE;
            }
        } else {
            $this->printInfo('Implementation of a function "searchAndDoAction" is skipped because data for the search is not specified ');
            $result = FALSE;
        }
        return $result;
    }

    /**
     * Search for multiple items
     *
     * @param <type> $tableContainer
     * @param <type> $searchElements
     * @param <type> $tableNumber 
     */
    public function multiRunSearch($tableContainer, $searchElements, $tableNumber)
    {
        $isArr = false;
        foreach ($searchElements as $key => $value) {
            $isArr = $isArr || is_array($value);
        }
        if ($isArr) {
            $i = 1;
            $qtyNewArrays = 0;
            foreach ($searchElements as $k => $v) {
                foreach ($v as $v1) {
                    if (count($v) > $qtyNewArrays) {
                        $qtyNewArrays = count($v);
                    }
                    ${'array' . $i}[$k] = $v1;
                    $i++;
                }
                $i = 1;
            }
            for ($y = 1; $y <= $qtyNewArrays; $y++) {
                $this->searchAndDoAction($tableContainer, ${'array' . $y}, 'mark', $tableNumber);
            }
        } else {
            $this->searchAndDoAction($tableContainer, $searchElements, 'mark', $tableNumber);
        }
    }

    /**
     * Click "Save" button and verify for errors.
     * 
     * @return boolean
     */
    function saveAndVerifyForErrors()
    {
        $result = false;
        $this->click($this->getUiElement("/admin/global/buttons/submit"));
        // check for error message
        if ($this->waitForElement($this->getUiElement('/admin/global/messages/error1'), 20)) {
            $etext = $this->getText($this->getUiElement('/admin/global/messages/error1'));
            $this->setVerificationErrors($etext);
        } elseif (!$this->verifyPageAndGetErrors()) {
            // Check for success message
            if ($this->waitForElement($this->getUiElement('/admin/messages/success'), 60)) {
                $etext = $this->getText($this->getUiElement('/admin/messages/success'));
                $this->printInfo($etext);
                $result = true;
            } else {
                $this->setVerificationErrors('No success message');
            }
        }
        return $result;
    }

    /**
     * Verifies if there are fields with errors at the open page. Receives error name and field name.
     * Works for pages which contain Tabs and order creation page.
     *
     * @return boolean
     */
    public function verifyPageAndGetErrors()
    {
        $result = false;
        $this->setUiNamespace("admin/global/elements/");
        if ($this->isElementPresent($this->getUiElement("tabs_container") . $this->getUiElement("tab_error"))) {
            $qtyTab = $this->getXpathCount($this->getUiElement("tabs_container") . $this->getUiElement("tab_error"));
            $isTab = 1;
        } elseif ($this->isElementPresent("//form" . $this->getUiElement("error_for_field"))) {
            if ($this->isElementPresent($this->getUiElement("containers_for_order"))) {
                $qtyTab = $this->getXpathCount($this->getUiElement("containers_for_order"));
                $isOrderPage = 1;
            } else {
                $qtyTab = $this->getXpathCount("//form//div[contains(@class,'fieldset')]");
                $isOrderPage = -1;
            }
            $isTab = 0;
        } else {
            $isTab = -1;
        }
        if ($isTab != -1) {
            $qtyErrors = $this->getXpathCount($this->getUiElement("error_for_field"));
            $this->printInfo("\r\n Found '" . $qtyErrors . "' error(s)");
            for ($y = 1; $y <= $qtyTab; $y++) {
                switch ($isTab) {
                    case 1:
                        $tabName = $this->getText($this->getUiElement("tabs_container") . $this->getUiElement("tab_error_many", $y));
                        $this->click($this->getUiElement("tabs_container") . $this->getUiElement("tab_error_many", $y));
                        $this->printInfo("'" . $tabName . "' tab contains invalid data:");
                        $errorXpath = $this->getUiElement("opened_tab") . $this->getUiElement("error_for_field");
                        break;
                    case 0:
                        switch ($isOrderPage) {
                            case 1:
                                $errorXpath = $this->getUiElement("containers_for_order") .
                                        "[$y]" . $this->getUiElement("error_for_field");
                                break;
                            case -1:
                                if ($this->isElementPresent($this->getUiElement("opened_tab"))) {
                                    $errorXpath = $this->getUiElement("opened_tab") . $this->getUiElement("error_for_field");
                                } else {
                                    $errorXpath = "//form" . $this->getUiElement("error_for_field");
                                }
                                break;
                        }
                        break;
                }
                $qtyFields = $this->getXpathCount($errorXpath);
                for ($i = 1; $i <= $qtyFields; $i++) {
                    if ($this->isElementPresent($errorXpath . "[$i]" . $this->getUiElement("field_name_with_error"))) {
                        $fieldName = $this->getText($errorXpath . "[$i]" . $this->getUiElement("field_name_with_error"));
                    } else {
                        $fieldName = $this->getAttribute($errorXpath . "[$i]" . "@id");
                        $fieldName = strrev($fieldName);
                        $fieldName = strrev(substr($fieldName, 0, strpos($fieldName, "-")));
                    }
                    $errorName = $this->getText($errorXpath . "[$i]");
                    $this->printInfo("\r\n Field '" . $fieldName . "' contains error - '" . $errorName . "'");
                }
            }
            $result = true;
        }
        return $result;
    }

    /**
     * Get value of the variable '$value'
     *
     * @param array $params
     * @param string $value
     * @return <type>
     */
    public function isSetValue($params, $value)
    {
        $Data = $params ? $params : $this->Data;
        if (isset($Data[$value])) {
            return $Data[$value];
        } elseif (isset($this->Data[$value])) {
            return $this->Data[$value];
        } else {
            //$this->printInfo("The value of the variable '" . $value . "' is not set");
            return NULL;
        }
    }

    /**
     * Data preparation
     *
     * @param array $params
     * @param string $searchWord
     * @return array
     */
    public function dataPreparation($params, $searchWord)
    {
        $Data = $params ? $params : $this->Data;
        $arrayName = array();
        foreach ($Data as $key => $value) {
            if (preg_match($searchWord, $key)) {
                if (is_array($value)) {
                    $i = 0;
                    foreach ($value as $v) {
                        $arrayName[$key][$i] = $v;
                        $i++;
                    }
                    $i = 0;
                } else {
                    $arrayName[$key] = $value;
                }
            }
        }
        return $arrayName;
    }

    /**
     * Delete opened element
     *
     * @param string $confirmation
     * @return boolean
     */
    public function doDeleteElement($confirmation)
    {
        $result = TRUE;
        if ($this->isElementPresent($this->getUiElement('/admin/global/buttons/delete'))) {
            $this->chooseCancelOnNextConfirmation();
            $this->click($this->getUiElement('/admin/global/buttons/delete'));
            if ($this->isConfirmationPresent()) {
                $text = $this->getConfirmation();
                if ($text == $confirmation) {
                    $this->chooseOkOnNextConfirmation();
                    $this->click($this->getUiElement('/admin/global/buttons/delete'));
                } else {
                    $this->printInfo('The confirmation text incorrect: ' . $text);
                    $result = FALSE;
                }
            } else {
                $this->printInfo('The confirmation does not appear');
            }
            if ($result) {
                if ($this->waitForElement($this->getUiElement('/admin/global/messages/error1'), 20)) {
                    $etext = $this->getText($this->getUiElement('/admin/global/messages/error1'));
                    $this->setVerificationErrors($etext);
                } elseif ($this->waitForElement($this->getUiElement('/admin/messages/success'), 30)) {
                    $etext = $this->getText($this->getUiElement('/admin/messages/success'));
                    $this->printInfo($etext);
                } else {
                    $this->setVerificationErrors('No success message');
                }
            }
        } else {
            $this->printInfo("There is no way to remove an item(There is no 'Delete' button)");
        }
        return $result;
    }

}

