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
        $loadingMask = $this->getUiElement('/admin/elements/progress_bar');

        // await for appear and disappear "Please wait" animated gif...
        for ($second = 0; ; $second++) {
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
     * Verify is there a value for the field and select it
     * @param  $field, $path, $number
     *
     * If $field is raised through Xpath expression such as //*[@id="input_%s"] than %s=$number
     * It is necessary to set UiNamespace before using a function
     * It is necessary to set NULL if the $number is not needed
     *
     * $field path - "UiNamespace"/selectors/"$field"
     */
    public function checkAndSelectField($params, $field, $number)
    {
        $result = true;
        $Data = $params ? $params : $this->Data;
        if (isset($Data[$field])) {
            if ($this->isElementPresent($this->getUiElement("selectors/" . $field, $number) .
                            $this->getUiElement("/admin/elements/option_for_field", $Data[$field]))) {
                $this->select($this->getUiElement("selectors/" . $field, $number), "label=" . $Data[$field]);
            } else {
                $this->setVerificationErrors("The value '" . $Data[$field] . "' cannot be set for the field '" . $field . "'");
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Verify is there a value for the field and fill it
     * @param  $field,$value, $number
     *
     * If $field is raised through Xpath expression such as //*[@id="selector_%s"] than %s=$number
     * It is necessary to set UiNamespace before using a function
     * It is necessary to set NULL if the $number is not needed
     *
     * $field path - "UiNamespace"/inputs/"$field"
     */
    public function checkAndFillField($params, $field, $number)
    {
        $Data = $params ? $params : $this->Data;
        if (isset($Data[$field])) {
            $this->type($this->getUiElement("inputs/" . $field, $number), $Data[$field]);
        }
    }

    /**
     * Search element(s) and perform action on them.
     *
     * @param $tableContainer, $searchElements, $action, $tableNumber
     *
     * If $tableContainer is raised through Xpath expression such as //*[@id="table_%s"] then %s=$tableNumber
     * When calling a function it is necessary to set NULL if the $tableNumber is not needed.
     * It is necessary to set UiNamespace before using a function.
     *
     * $searchElements - ARRAY which contains search elements.
     * $action can have only 2 values: "mark"(mark the found element) and "open"(Open the found element)
     *
     * Пример задания $searchElements: ar = array('searchBy1'=>'value1','searchBy2'=>'value2','searchBy2'=>'value2');
     * где 'searchBy1', 'searchBy2', .. путь к XPath поля, для которого задается значение 'value1'. Пока работает только
     * с полями ввода.
     *
     * Принцип задания 'searchBy1' => XPath: UiNamespace/inputs/'searchBy1'
     * Принцип задания $tableContainer => XPath: UiNamespace/elements/$tableContainer
     *
     */
    public function searchAndDoAction($tableContainer, $searchElements, $action, $tableNumber)
    {
        $result = TRUE;
        if (is_array($searchElements) and count($searchElements) > 0) {
            foreach ($searchElements as $key => $value) {
                $this->type($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                        $this->getUiElement("inputs/" . $key), $value);
            }
            $this->click($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                    $this->getUiElement("/admin/buttons/search"));
            $this->pleaseWait();
            if ($this->isTextPresent($this->getUiElement('/admin/elements/no_records'))) {
                $this->printInfo($this->getUiElement('/admin/elements/no_records'));
                $result = FALSE;
            } elseif ($action == "mark") {
                $this->click($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                        $this->getUiElement('/admin/elements/mark_filtered_element', $searchElements));
            } elseif ($action == "open") {
                $this->click($this->getUiElement("elements/" . $tableContainer, $tableNumber) .
                        $this->getUiElement('/admin/elements/filtered_element', $searchElements));
                !$this->isElementPresent($this->getUiElement("elements/" . $tableContainer, $tableNumber), 20);
            }
        } else {
            $this->printInfo('Implementation of a function "searchAndDoAction" is skipped because data for the search is not specified ');
            $result = FALSE;
        }
        return $result;
    }

    /**
     * Click button $saveButton and verify for errors
     *
     * @param $saveButton
     * It is necessary to set UiNamespace before using a function.
     * $saveButton path - "UiNamespace"/elements/"$saveButton"
     */
    function saveAndVerifyForErrors()
    {
        $this->click($this->getUiElement("/admin/buttons/submit"));
        // check for error message
        if ($this->waitForElement($this->getUiElement('/admin/messages/error'), 25)) {
            $etext = $this->getText($this->getUiElement('/admin/messages/error'));
            $this->setVerificationErrors($etext);
        } else {
            if (!$this->verifyTabsForErrors()) {
                // Check for success message
                if ($this->waitForElement($this->getUiElement('/admin/messages/success'), 60)) {
                    $etext = $this->getText($this->getUiElement('/admin/messages/success'));
                    $this->printInfo($etext);
                } else {
                    $this->setVerificationErrors('No success message');
                }
            }
        }
    }

    /**
     * Verify on opened page Tabs for errors
     *
     */
    public function verifyTabsForErrors()
    {
        $result = false;
        if ($this->isElementPresent($this->getUiElement("/admin/elements/tabs_container") .
                        $this->getUiElement("/admin/elements/tab_error"))) {
            $qtyTab = $this->getXpathCount($this->getUiElement("/admin/elements/tabs_container") .
                            $this->getUiElement("/admin/elements/tab_error"));
            $qtyErrors = $this->getXpathCount($this->getUiElement("/admin/elements/error_for_field"));
            $this->printInfo("Found '" . $qtyErrors . "' errors");
            for ($y = 1; $y <= $qtyTab; $y++) {
                $tabName = array();
                $tabName[$y] = $this->getText($this->getUiElement("/admin/elements/tabs_container") .
                                $this->getUiElement("/admin/elements/tab_error_many", $y));
                $this->click($this->getUiElement("/admin/elements/tabs_container") .
                        $this->getUiElement("/admin/elements/tab_error_many", $y));
                $this->printInfo("'" . $tabName[$y] . "' tab contains invalid data:");
                $this->getErrorsInTab();
            }
            $result = true;
        }
        if ($this->isElementPresent($this->getUiElement("/admin/elements/opened_tab") .
                        $this->getUiElement("/admin/elements/error_for_field"))) {
            $this->verifyPageForErrors();
            $result = true;
        }
        return $result;
    }

    /**
     * Verify page for errors
     *
     */
    public function verifyPageForErrors()
    {
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');

        $qtyContainer = $this->getXpathCount($this->getUiElement('elements/containers'));
        $this->printInfo("TABOV!!!" . $qtyContainer);
        for ($i = 1; $i <= $qtyContainer; $i++) {
            $qtyErrorsInContainer = $this->getXpathCount($this->getUiElement('elements/containers') . "[$i]" .
                            $this->getUiElement('/admin/elements/error_for_field'));
            $this->printInfo($i . "-ERROROV!!!" . $qtyErrorsInContainer);
            if ($qtyErrorsInContainer > 0) {
                for ($y = 1; $y <= $qtyErrorsInContainer; $y++) {
                    if ($this->isElementPresent($this->getUiElement('elements/containers') . "[$i]" .
                                    $this->getUiElement('/admin/elements/error_for_field') . "[$y]" .
                                    $this->getUiElement("/admin/elements/field_name_with_error"))) {
                        $fieldName = $this->getText($this->getUiElement('elements/containers') . "[$i]" .
                                        $this->getUiElement('/admin/elements/error_for_field') . "[$y]" .
                                        $this->getUiElement("/admin/elements/field_name_with_error"));
                    } else {
                        $fieldName = $this->getAttribute($this->getUiElement('elements/containers') . "[$i]" .
                                        $this->getUiElement('/admin/elements/error_for_field') . "[$y]" . "@id");
                    }
                    $errorName = $this->getText($this->getUiElement('elements/containers') . "[$i]" .
                                    $this->getUiElement('/admin/elements/error_for_field') . "[$y]");
                    $this->printInfo("Field '" . $fieldName . "' contains error - '" . $errorName . "'");
                }
            }
        }
    }

    /**
     * Get fields name and error for this fields on tab(work for Product ->General, Prices, Inventory tabs
     * and Attribute->Properties tab)
     *
     */
    public function getErrorsInTab()
    {
        $qtyFields = $this->getXpathCount($this->getUiElement("/admin/elements/opened_tab") .
                        $this->getUiElement("/admin/elements/error_for_field"));
        for ($i = 1; $i <= $qtyFields; $i++) {
            if ($this->isElementPresent($this->getUiElement("/admin/elements/opened_tab") .
                            $this->getUiElement("/admin/elements/error_for_field_many", $i) .
                            $this->getUiElement("/admin/elements/field_name_with_error"))) {
                $fieldName = $this->getText($this->getUiElement("/admin/elements/opened_tab") .
                                $this->getUiElement("/admin/elements/error_for_field_many", $i) .
                                $this->getUiElement("/admin/elements/field_name_with_error"));
            } else {
                $fieldName = $this->getAttribute($this->getUiElement("/admin/elements/opened_tab") .
                                $this->getUiElement("/admin/elements/error_for_field_many", $i) . "@id");
            }
            $errorName = $this->getText($this->getUiElement("/admin/elements/opened_tab") .
                            $this->getUiElement("/admin/elements/error_for_field_many", $i));
            $this->printInfo("Field '" . $fieldName . "' contains error - '" . $errorName . "'");
        }
    }

}

