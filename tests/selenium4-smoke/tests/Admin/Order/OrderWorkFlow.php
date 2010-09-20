<?php

class Admin_Order_OrderWorkFlow extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/order');
        $this->setUiNamespace();
    }

    /**
     * Test Suite for testing creation order, invoice, shippment, credit memo
     * and reorder.
     * Checking the status of order after performing each of the case (e.g. "creation order")
     */
    function testOrderWorkFlow() {
        if ($this->model->doLogin()) {
            $ordNum = $this->model->doCreateOrder();
            $this->model->doOpenOrder($ordNum);
            $this->model->doCreateInvoice($ordNum);
            $this->model->doOpenOrder($ordNum);
            $this->model->doCreateShippment($ordNum);
            $this->model->doOpenOrder($ordNum);
            $this->model->doCreateCreditMemo($ordNum);
            $this->model->doOpenOrder($ordNum);
            $this->model->doReOrder();
        }
    }

}
