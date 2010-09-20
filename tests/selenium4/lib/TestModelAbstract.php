<?php
/**
 * Abstract framework model
 *
 * @author Magento Inc.
 */
abstract class TestModelAbstract
{
    /**
     * Caller TestCase instance
     *
     * @var TestCaseAbstract
     */
    protected $_testCase = null;

    /**
     * Framework model consturctor
     *
     * @param TestCaseAbstract $instance
     */
    public function  __construct(TestcaseAbstract $instance) {
        $this->_testCase = $instance;
    }

    /**
     * Simple overloading mechanism
     * Allows to call all testCase methods from the model context directly
     *
     * @param string $methodName
     * @param array $args
     * @method unknown  addLocationStrategy()
     * @method unknown  addLocationStrategyAndWait()
     * @method unknown  addScript()
     * @method unknown  addScriptAndWait()
     * @method unknown  addSelection()
     * @method unknown  addSelectionAndWait()
     * @method unknown  allowNativeXpath()
     * @method unknown  allowNativeXpathAndWait()
     * @method unknown  altKeyDown()
     * @method unknown  altKeyDownAndWait()
     * @method unknown  altKeyUp()
     * @method unknown  altKeyUpAndWait()
     * @method unknown  answerOnNextPrompt()
     * @method unknown  assignId()
     * @method unknown  assignIdAndWait()
     * @method unknown  attachFile()
     * @method unknown  break()
     * @method unknown  captureEntirePageScreenshot()
     * @method unknown  captureEntirePageScreenshotAndWait()
     * @method unknown  captureEntirePageScreenshotToStringAndWait()
     * @method unknown  captureScreenshotAndWait()
     * @method unknown  captureScreenshotToStringAndWait()
     * @method unknown  check()
     * @method unknown  checkAndWait()
     * @method unknown  chooseCancelOnNextConfirmation()
     * @method unknown  chooseCancelOnNextConfirmationAndWait()
     * @method unknown  chooseOkOnNextConfirmation()
     * @method unknown  chooseOkOnNextConfirmationAndWait()
     * @method unknown  click()
     * @method unknown  clickAndWait()
     * @method unknown  clickAt()
     * @method unknown  clickAtAndWait()
     * @method unknown  close()
     * @method unknown  contextMenu()
     * @method unknown  contextMenuAndWait()
     * @method unknown  contextMenuAt()
     * @method unknown  contextMenuAtAndWait()
     * @method unknown  controlKeyDown()
     * @method unknown  controlKeyDownAndWait()
     * @method unknown  controlKeyUp()
     * @method unknown  controlKeyUpAndWait()
     * @method unknown  createCookie()
     * @method unknown  createCookieAndWait()
     * @method unknown  deleteAllVisibleCookies()
     * @method unknown  deleteAllVisibleCookiesAndWait()
     * @method unknown  deleteCookie()
     * @method unknown  deleteCookieAndWait()
     * @method unknown  deselectPopUp()
     * @method unknown  deselectPopUpAndWait()
     * @method unknown  doubleClick()
     * @method unknown  doubleClickAndWait()
     * @method unknown  doubleClickAt()
     * @method unknown  doubleClickAtAndWait()
     * @method unknown  dragAndDrop()
     * @method unknown  dragAndDropAndWait()
     * @method unknown  dragAndDropToObject()
     * @method unknown  dragAndDropToObjectAndWait()
     * @method unknown  dragDrop()
     * @method unknown  dragDropAndWait()
     * @method unknown  echo()
     * @method unknown  fireEvent()
     * @method unknown  fireEventAndWait()
     * @method unknown  focus()
     * @method unknown  focusAndWait()
     * @method string   getAlert()
     * @method array    getAllButtons()
     * @method array    getAllFields()
     * @method array    getAllLinks()
     * @method array    getAllWindowIds()
     * @method array    getAllWindowNames()
     * @method array    getAllWindowTitles()
     * @method string   getAttribute()
     * @method array    getAttributeFromAllWindows()
     * @method string   getBodyText()
     * @method string   getConfirmation()
     * @method string   getCookie()
     * @method string   getCookieByName()
     * @method integer  getCursorPosition()
     * @method integer  getElementHeight()
     * @method integer  getElementIndex()
     * @method integer  getElementPositionLeft()
     * @method integer  getElementPositionTop()
     * @method integer  getElementWidth()
     * @method string   getEval()
     * @method string   getExpression()
     * @method string   getHtmlSource()
     * @method string   getLocation()
     * @method string   getLogMessages()
     * @method integer  getMouseSpeed()
     * @method string   getPrompt()
     * @method array    getSelectOptions()
     * @method string   getSelectedId()
     * @method array    getSelectedIds()
     * @method string   getSelectedIndex()
     * @method array    getSelectedIndexes()
     * @method string   getSelectedLabel()
     * @method array    getSelectedLabels()
     * @method string   getSelectedValue()
     * @method array    getSelectedValues()
     * @method unknown  getSpeed()
     * @method unknown  getSpeedAndWait()
     * @method string   getTable()
     * @method string   getText()
     * @method string   getTitle()
     * @method string   getValue()
     * @method boolean  getWhetherThisFrameMatchFrameExpression()
     * @method boolean  getWhetherThisWindowMatchWindowExpression()
     * @method integer  getXpathCount()
     * @method unknown  goBack()
     * @method unknown  goBackAndWait()
     * @method unknown  highlight()
     * @method unknown  highlightAndWait()
     * @method unknown  ignoreAttributesWithoutValue()
     * @method unknown  ignoreAttributesWithoutValueAndWait()
     * @method boolean  isAlertPresent()
     * @method boolean  isChecked()
     * @method boolean  isConfirmationPresent()
     * @method boolean  isCookiePresent()
     * @method boolean  isEditable()
     * @method boolean  isElementPresent()
     * @method boolean  isOrdered()
     * @method boolean  isPromptPresent()
     * @method boolean  isSomethingSelected()
     * @method boolean  isTextPresent()
     * @method boolean  isVisible()
     * @method unknown  keyDown()
     * @method unknown  keyDownAndWait()
     * @method unknown  keyDownNative()
     * @method unknown  keyDownNativeAndWait()
     * @method unknown  keyPress()
     * @method unknown  keyPressAndWait()
     * @method unknown  keyPressNative()
     * @method unknown  keyPressNativeAndWait()
     * @method unknown  keyUp()
     * @method unknown  keyUpAndWait()
     * @method unknown  keyUpNative()
     * @method unknown  keyUpNativeAndWait()
     * @method unknown  metaKeyDown()
     * @method unknown  metaKeyDownAndWait()
     * @method unknown  metaKeyUp()
     * @method unknown  metaKeyUpAndWait()
     * @method unknown  mouseDown()
     * @method unknown  mouseDownAndWait()
     * @method unknown  mouseDownAt()
     * @method unknown  mouseDownAtAndWait()
     * @method unknown  mouseMove()
     * @method unknown  mouseMoveAndWait()
     * @method unknown  mouseMoveAt()
     * @method unknown  mouseMoveAtAndWait()
     * @method unknown  mouseOut()
     * @method unknown  mouseOutAndWait()
     * @method unknown  mouseOver()
     * @method unknown  mouseOverAndWait()
     * @method unknown  mouseUp()
     * @method unknown  mouseUpAndWait()
     * @method unknown  mouseUpAt()
     * @method unknown  mouseUpAtAndWait()
     * @method unknown  mouseUpRight()
     * @method unknown  mouseUpRightAndWait()
     * @method unknown  mouseUpRightAt()
     * @method unknown  mouseUpRightAtAndWait()
     * @method unknown  open()
     * @method unknown  openWindow()
     * @method unknown  openWindowAndWait()
     * @method unknown  pause()
     * @method unknown  refresh()
     * @method unknown  refreshAndWait()
     * @method unknown  removeAllSelections()
     * @method unknown  removeAllSelectionsAndWait()
     * @method unknown  removeScript()
     * @method unknown  removeScriptAndWait()
     * @method unknown  removeSelection()
     * @method unknown  removeSelectionAndWait()
     * @method unknown  retrieveLastRemoteControlLogs()
     * @method unknown  rollup()
     * @method unknown  rollupAndWait()
     * @method unknown  runScript()
     * @method unknown  runScriptAndWait()
     * @method unknown  select()
     * @method unknown  selectAndWait()
     * @method unknown  selectFrame()
     * @method unknown  selectPopUp()
     * @method unknown  selectPopUpAndWait()
     * @method unknown  selectWindow()
     * @method unknown  setBrowserLogLevel()
     * @method unknown  setBrowserLogLevelAndWait()
     * @method unknown  setContext()
     * @method unknown  setCursorPosition()
     * @method unknown  setCursorPositionAndWait()
     * @method unknown  setMouseSpeed()
     * @method unknown  setMouseSpeedAndWait()
     * @method unknown  setSpeed()
     * @method unknown  setSpeedAndWait()
     * @method unknown  shiftKeyDown()
     * @method unknown  shiftKeyDownAndWait()
     * @method unknown  shiftKeyUp()
     * @method unknown  shiftKeyUpAndWait()
     * @method unknown  shutDownSeleniumServer()
     * @method unknown  store()
     * @method unknown  submit()
     * @method unknown  submitAndWait()
     * @method unknown  type()
     * @method unknown  typeAndWait()
     * @method unknown  typeKeys()
     * @method unknown  typeKeysAndWait()
     * @method unknown  uncheck()
     * @method unknown  uncheckAndWait()
     * @method unknown  useXpathLibrary()
     * @method unknown  useXpathLibraryAndWait()
     * @method unknown  waitForCondition()
     * @method unknown  waitForPageToLoad()
     * @method unknown  waitForPopUp()
     * @method unknown  windowFocus()
     * @method unknown  windowMaximize()
     * @method TestModelAbstract getModel(string $modelName)
     * @method boolean waitForElement(string $xpath, int $timeforwait)
     * @method void setVerificationErrors(string $error)
     * @method string getUiElement(string $elem, array|void $arg)
     * @method void setUiNamespace(string|void $namespace)
     * @method void printInfo(string $line)
     * @method void printDebug(string $line)
     * @method void printError(string $line)
     */
    public function __call($methodName, $args)
    {
        return call_user_func_array(
            array($this->_testCase, $methodName),
            $args
        );
    }

    /**
     * Simple overloading mechanism
     * Returns a variable value taken from testCase
     *
     * @param string $name
     * @return mixed
     */
    public function  __get($name) {
        return $this->_testCase->__get($name);
    }

    /**
     * Simple overloading mechanism
     * Sets a value to a testCase variable
     *
     * @param string $name
     * @param mixed $value
     */
    public function  __set($name, $value) {
        $this->_testCase->__set($name, $value);
    }

    /**
     * Simple overloading mechanism
     * Returns true in case the testCase variable exists
     *
     * @param string $name
     * @return boolean
     */
    public function  __isset($name) {
        return $this->_testCase->__isset($name);
    }

    /**
     * Simple overloading mechanism
     * Unsets a testCase variable
     *
     * @param string $name
     */
    public function __unset($name) {
        $this->_testCase->__unset($name);
    }

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        $this->_testCase->setBrowser((string)Core::getEnvConfig('browser', '*firefox'));
        $this->_testCase->setHost((string)Core::getEnvConfig('host', '127.0.0.1'));
        $this->_testCase->setPort((int)Core::getEnvConfig('port', 4444));
        $this->_testCase->setTimeout((int)Core::getEnvConfig('timeOut', 60));
        $this->_testCase->setSleep((int)Core::getEnvConfig('sleepTime', 0));
    }

    /**
     * Select $countryName in countries dropdown
     * @param $tableBaseURL - xpath for table with address fields
     * @param $countryName - country name
     * @return boolean
     */
    public function selectCountry($selectorID, $countryName)
    {
        $paramsArray = array (
            '$selectorID' => $selectorID,
            '$countryName'  => $countryName
        );
//        Core::debug($this->getUiElement('elements/selectedCountry',$paramsArray));
        if (!$this->isElementPresent($this->getUiElement('elements/selectedCountry',$paramsArray))) {
            $this->select($selectorID, $countryName);
//            Required  for Admin !
//            $this->pleaseWait();
            return true;
        } else {
            $this->select($selectorID, $countryName);
           return false;
        }
    }

   /**
     * Select $regionName in region dropdown
     * @param $selectorID - xpath for element
     * @param $regionName - region name
     * @return boolean
     */
    public function selectRegion($selectorID, $regionName)
    {
        $paramsArray = array (
            "$selectorID" => $selectorID,
            "$regionName" => $regionName
        );
        if ($this->isElementPresent("//select[@id='" . $selectorID . "' and contains(@style,'display: none')]")) {
            // region selector is input
            $this->printDebug("region field is input\n".$selectorID."\n");
            $this->type("billing:region",$regionName);
            return true;
        } else {
            // region selector have "dropdown" type
            $this->printDebug("region field is dropdown");
            if (!$this->isElementPresent($this->getUiElement('/elements/selectedRegion',$paramsArray))) {
                $this->select($selectorID, $regionName);
             return false;
            }
        }
    }

    /*
     * Return index of address matched to specified $mask.
     * @param $selector
     * @param $mask
     * @return address index or -1 when not
     */
    public function findAddressByMask($selector, $mask)
    {
            $this->printDebug('findAddressByMask started');
            $addressCount = $this->getXpathCount($selector.'/option');
            $this->printDebug($addressCount . ' addresses founded');

            for ($i=1;$i<=$addressCount;$i++) {
                $xpath = $selector . "/option[".$i."]";
                $addressValue = $this->getText($xpath);
                if (preg_match($mask, $addressValue)) {
                    return $i;
                }
            };
            $this->printDebug('NOT matched');
            return -1;
    }
}

