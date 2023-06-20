<?php
class InvoicesController extends Controller
{
    
    public function __construct($model, $action) {   
        parent::__construct($model, $action);
        $this->_setModel("invoices");
    }

    public function index() {
        
        try {

            $customerList = new CustomersModel();
            $customers = $customerList->getNameList();
            $this->_view->set('customers', $customers);

            //$invoices = $this->_model->getList();
            //$this->_view->set('invoices', $invoices);
            $this->_view->set('title', 'Invoice List');
            
            
            return $this->_view->output();
            
        } catch (Exception $e) {
            echo "Application error:" . $e->getMessage();
        }
    }
    
    public function create() {
        try {
            $this->_view->set('title', 'Create Invoice');
            
            $customerList = new CustomersModel();
            $customers = $customerList->getNameList();
            $this->_view->set('customers', $customers);
            
            $groupTbl = new CustomerGroupsModel();
            $groups = $groupTbl->list();
            $this->_view->set('groups', $groups);
            
            if(!empty($_POST)) {
                $data = $_POST;
                
                // echo '<pre>'; print_r($data); exit;

                $isProformaInvoice = (isset($data['proforma']) && $data['proforma'] == 1) ? true : false;

                $invoiceeData = array();
                $invoiceItems = array();
                
                $invoiceeData['group_id'] = $data['group_id'];
                $invoiceeData['customer_id'] = $data['customer_id'];
                $invoiceeData['order_id'] = $data['order_id'];
                $invoiceeData['invoice_date'] = $data['invoice_date'];
                $invoiceeData['po_no'] = $data['po_no'];
                $invoiceeData['sales_person'] = $data['sales_person'];
                $invoiceeData['bill_to'] = $data['bill_to'];
                $invoiceeData['ship_to'] = $data['ship_to'];
                $invoiceeData['order_total'] = $data['order_total'];
                $invoiceeData['sub_total'] = $data['sub_total'];
                $invoiceeData['sgst'] = $data['sgst'];
                $invoiceeData['cgst'] = $data['cgst'];
                $invoiceeData['igst'] = $data['igst'];
                $invoiceeData['invoice_total'] = $data['invoice_total'];
                $invoiceeData['payment_term'] = isset($data['payment_term']) ? $data['payment_term'] : null ;
                $invoiceeData['pay_percent'] = isset($data['pay_percent']) ? $data['pay_percent'] : null ;
                $invoiceeData['payment_description'] = isset($data['payment_description']) ? $data['payment_description'] : null ;
                $invoiceeData['remarks'] = $data['remarks'];
                $invoiceeData['due_date'] = $data['due_date'];
                //$invoiceeData['invoice_no'] = $this->genInvoiceNo();
                $invoiceeData['invoice_no'] = $data['invoice_no'];

                $invoiceeData['user_id'] = $this->_session->get('user_id'); // created by user
                
                
                
                /*
                if(isset($data['item'])) {
                    foreach($data['item'] as $key => $item) {
                        $row = array();
                        $row['order_item_id'] = $data['order_item_id'][$key];
                        $row['item'] = $data['item'][$key];
                        $row['description'] = $data['description'][$key];
                        $row['qty'] = $data['qty'][$key];
                        $row['uom_id'] = $data['uom'][$key];
                        $row['unit_price'] = $data['unit_price'][$key];
                        $row['total'] = $data['total'][$key];
    
                        $invoiceItems[] = $row;
                    }
                } */

                foreach($data['order_details'] as $item) {
                    $orderItem = array();
                    $orderItem['order_item_id'] = $item['order_item_id'];
                    $orderItem['order_payterm_id'] = $item['order_payterm_id'];
                    $orderItem['item'] = $item['item'];
                    if($isProformaInvoice == false) {
                        $orderItem['proforma_invoice_item_id'] = $item['proforma_invoice_item_id'];
                    }
                    $orderItem['description'] = $item['description'];
                    $orderItem['qty'] = $item['qty'];
                    $orderItem['uom_id'] = $item['uom_id'];
                    $orderItem['unit_price'] = $item['unit_price'];
                    $orderItem['total'] = $item['total'];
                    $orderItem['hsn_id'] = $item['hsn_id'];

                    $invoiceItems[] = $orderItem;
                }
                
                $hide_po = isset($data['hidepo']) ? ($data['hidepo'] == "1"? true: false) : false;

                if($isProformaInvoice) {
                    $tblProformaInvoice = new ProformaInvoicesModel();
                    $invoiceId = $tblProformaInvoice->save($invoiceeData);
                    if($invoiceId) {
                        $tblInvoiceItem = new ProformaInvoiceItemsModel();
                        foreach($invoiceItems as $invoiceItem) {
                            $invoiceItem['proforma_invoice_id'] = $invoiceId;
                            $tblInvoiceItem->save($invoiceItem);
                        }
                        
                        $this->geninv($invoiceId, true, true, $hide_po);

                        $_SESSION['message'] = 'Invoice added successfully';
                        header("location:". ROOT. "invoices"); 
                    } else {
                        $_SESSION['error'] = 'Fail to add invoice';
                    }
                } else {
                    $invoiceId = $this->_model->save($invoiceeData);
                    if($invoiceId) {
                        $tblInvoiceItem = new InvoiceItemsModel();
                        $tblPayments = new PaymentsModel();
                        foreach($invoiceItems as $invoiceItem) {
                            if($isProformaInvoice == false && $invoiceItem['proforma_invoice_item_id'] != 0) {
                                $tblPayments->upd_paytrm_inv_id($invoiceItem['proforma_invoice_item_id'], $invoiceId);
                            }
                            $invoiceItem['invoice_id'] = $invoiceId;
                            $tblInvoiceItem->save($invoiceItem);
                        }
                        
                        // $this->geninv($invoiceId, true, false, $hide_po); 
                        $customerTbl = new CustomersModel();
                        $customer = $customerTbl->get($invoiceeData['customer_id']);
                        $this->postEinvoiceRequest($invoiceeData, $invoiceItems, $customer);

                        $_SESSION['message'] = 'Invoice added successfully';
                        header("location:". ROOT. "invoices"); 
                    } else {
                        $_SESSION['error'] = 'Fail to add invoice';
                    }
                }
                
            }
            
            return $this->_view->output();
            
        } catch (Exception $e) {
            echo "Application error:" . $e->getMessage();
        }
    }
    
    
    public function view($id) {
        try {
            $this->_view->set('title', 'View Invoice');
            
            $invoice = $this->_model->get($id);
            $this->_view->set('invoice', $invoice);
            
            $invoiceItemTbl = new InvoiceItemsModel();
            $invoiceItems = $invoiceItemTbl->getListByInvoiceId($id);
            $this->_view->set('invoiceItems', $invoiceItems);
            
            $paymentTbl = new PaymentsModel();
            $payments = $paymentTbl->getDetailsByInvoiceId($id);
            
            $this->_view->set('payments', $payments);
            
            
            $customerTbl = new CustomersModel();
            $customer = $customerTbl->get($invoice['customer_id']);
            $this->_view->set('customer', $customer);
            
            $customerShipTo = $customerTbl->get($invoice['ship_to']);
            $this->_view->set('shipToAddress', $customerShipTo['address']);
            
            return $this->_view->output();
            
        } catch (Exception $e) {
            echo "Application error:" . $e->getMessage();
        }
    }
    
    public function getTaxesRate($customerId) {
        try {
            $customer = new CustomersModel();
            $customer = $customer->get($customerId);
            
            $company = new CompanyModel();
            $company = $company->get(1);
            
            $itMaster = new ItMasterModel();
            $itMaster = $itMaster->get(1);
            
            
            $result = array();
            
            if($customer['state'] == $company['state']) {
                $result['state'] = 'same';
                $result['cgst'] = $itMaster['cgst'];
                $result['sgst'] = $itMaster['sgst'];
            } else {
                $result['state'] = 'other';
                $result['igst'] = $itMaster['igst'];
            }
            echo json_encode($result); exit;
            
            
        } catch (Exception $e) {
            echo "Application error:" . $e->getMessage();
        }
    }

    public function geninv($invoiceId=null, $proformaSwitch = false, $createpdf=false, $hidepo=false){
        $dataItem = array();
        $invoice = array();
        $invoiceItems = array();
        $tblProformaInvoice = new ProformaInvoicesModel();
        $customerTbl = new CustomersModel();
        $orderTable = new OrdersModel();
        $company = new CompanyModel();
        $hsn = new HsnModel();
        if(!empty($_POST)) {
            $data = $_POST;

            // echo '<pre>'; print_r($data); exit;
            
            if (!$invoiceId){ $invoiceId = ($this->_model->getLastId() + 1); }
            $isProformaInvoice = (isset($data['proforma']) && $data['proforma'] == 1) ? true : false;
            
            $invoice['invoice_no'] = $data['invoice_no'];
            $invoice['group_id'] = $data['group_id'];
            $invoice['customer_id'] = $data['customer_id'];
            $invoice['order_id'] = $data['order_id'];
            $invoice['invoice_date'] = $data['invoice_date'];
            $invoice['po_no'] = $data['po_no'];
            $invoice['sales_person'] = $data['sales_person'];
            $invoice['bill_to'] = $data['bill_to'];
            $invoice['ship_to'] = $data['ship_to'];
            $invoice['order_total'] = $data['order_total'];
            $invoice['sub_total'] = $data['sub_total'];
            $invoice['sgst'] = $data['sgst'];
            $invoice['cgst'] = $data['cgst'];
            $invoice['igst'] = $data['igst'];
            $invoice['invoice_total'] = $data['invoice_total'];
            $invoice['payment_term'] = isset($data['payment_term']) ? $data['payment_term'] : null ;
            $invoice['pay_percent'] = isset($data['pay_percent']) ? $data['pay_percent'] : null ;
            $invoice['payment_description'] = isset($data['payment_description']) ? $data['payment_description'] : null ;
            $invoice['remarks'] = $data['remarks'];

            foreach($data['order_details'] as $item) {
                $orderItem = array();
                $orderItem['order_item_id'] = $item['order_item_id'];
                $orderItem['order_payterm_id'] = $item['order_payterm_id'];
                $orderItem['item'] = $item['item'];
                $orderItem['description'] = $item['description'];
                $orderItem['qty'] = $item['qty'];
                $orderItem['uom_id'] = $item['uom_id'];
                $orderItem['hsn_id'] = $item['hsn_id'];
                $orderItem['unit_price'] = $item['unit_price'];
                $orderItem['total'] = $item['total'];
                if($item['total'] > 0) { $invoiceItems[] = $orderItem; }
            }

        } else {
            $invoice = $this->_model->get($invoiceId);
            $invoiceItems = $this->_model->getInvoiceItem($invoiceId);
            if ($proformaSwitch && !$invoiceId){
                $invoice = $tblProformaInvoice->get($invoiceId);
                $invoiceItems = $tblProformaInvoice->getInvoiceItem($invoiceId);
            }
        }

        $company = $company->get(1);
        
        $customer = $customerTbl->get($invoice['customer_id']);
        $customerShipTo = $customerTbl->get($invoice['ship_to']);

        $order = $orderTable->get($invoice['order_id']);
        $oderItems = $orderTable->getOrderItem($invoice['order_id']);
        
        $print_uom_qty= '<th>HSN Code</th><th>Qty.</th><th>Unit</th>';
        if(in_array($order['order_type'], array(1, 2, 3))) { $print_uom_qty= '<th>HSN Code</th><th></th><th></th>'; }
        if(in_array($order['order_type'], array(1, 2, 3, 4, 5, 6, 7, 99))) {
            $tempInvoiceItem = [];
            foreach($invoiceItems as $tempItem)
            {
                // if(in_array($order['order_type'], array(1, 2, 3)))
                // {
                //     $print_uom_qty= '<th>HSN Code</th><th></th><th></th>';
                //     $tempItem['qty'] = '';
                // }
                array_push($tempInvoiceItem,$tempItem);
            }
            $dataItem = $tempInvoiceItem;
        } else { $dataItem =  $oderItems; }

        $vars = array(
            "{{INV_DATE}}" => date('d/m/Y', strtotime($invoice['invoice_date'])),
            "{{COMPANY_BILLTO}}" => addressmaker($company['address']),
            "{{BILLTO_ADDRESS}}" => addressmaker($company['address'], 3),
            "{{COMP_TEL}}" => $company['contact'],
            "{{COMP_PAN}}" => $company['pan'],
            "{{COMP_SAC}}" => $company['sac'],
            "{{COMP_GSTIN}}" => $company['gstin'],
            "{{COMP_BANK}}" => $company['bank_name'],
            "{{COMP_ACCNO}}" => $company['account_no'],
            "{{COMP_IFSC}}" => $company['ifsc_code'],
            "{{PO_NO}}" => "Purchase Order No.: ".$invoice['po_no'],
            "{{ORDER_TYPE}}" => $print_uom_qty,
            "{{PO_DATE}}" => date('d/m/Y', strtotime($order['order_date'])),
            "{{CUST_ADDRESS}}" =>"<b>" . $customer['name']."</b><br />". addressmaker($customer['address'], 3),
            "{{CUST_TEL}}" => $customer['pphone'],
            "{{DECLARATION}}" => getdeclaration($customer['declaration']),
            "{{CUST_FAX}}" => $customer['fax'],
            "{{CUST_PAN}}" => $customer['pan'],
            "{{CUST_GST}}" => $customer['gstin'],
            "{{CUST_SHIPTO}}" => "<b>" . $customer['name']."</b><br />". addressmaker($customerShipTo['address'], 3),
            "{{CUST_CONT_PERSON}}" => $invoice['sales_person'],
            "{{INV_TOTAL}}" => number_format($invoice['invoice_total'], 2),
            "{{AMOUNT_WORD}}" => $this->_utils->AmountInWords($invoice['invoice_total']),
        );
        
        if ($proformaSwitch){ $vars["{{INV_NO}}"] = "PI No.: PI".$invoice['invoice_no']; }
        else { $vars["{{INV_NO}}"] = "Invoice No: ".$invoice['invoice_no']; }

        if ($hidepo){ $vars["{{PO_NO}}"] = ""; }

        $orderBaseTotal = 0.00;
        $itemList = '';
        foreach($dataItem as $key => $item) {
            $hsncode = $hsn->get($item['hsn_id']);
            $itemList .= '<tr>
            <td class="txtc">'.($key+1).'</td>
            <td class="txtc">'.$item['description'].'</td>
            <td class="txtc">'.$hsncode['code'].'</td>
            <td class="txtc">'.$item['qty'].'</td>
            <td class="txtc">'.number_format($item['unit_price'], 2).'</td>
            <td class="txtc">'.number_format($item['total'], 2).'</td></tr>';
            $orderBaseTotal += $item['total'];
        }

        // echo '<pre>'; print_r($itemList); exit;

        if(in_array($order['order_type'], array(6))){ $vars["{{TDS}}"] = ""; }
        else { $vars["{{TDS}}"] = "<li>TDS should be Deduct @10% As per Sec.194J.</li>"; }

        $taxName = '';
        $taxesLayout = '';
        if((int)$invoice['igst']) {
            $taxName ="IGST @ 18%";
            $taxesLayout = number_format($invoice['igst'], 2);
        } else {
            $taxName ="CGST @ 9%<br />SGST @ 9%";
            $taxesLayout = number_format($invoice['cgst'], 2).'<br />'.number_format($invoice['sgst'], 2);
        }
        
        $vars["{{GST_LABEL}}"] = $taxName;
        $vars["{{GST_VALUE}}"] = $taxesLayout;
        $vars["{{ITEM_LIST}}"] = $itemList;
        $vars["{{ORDER_TOTAL}}"] = number_format($orderBaseTotal, 2);

        $messageBody = strtr(file_get_contents('./assets/mail_template/invoiceTemplate.html'), $vars);
        if (!$createpdf){echo $messageBody;}
        else{
            require_once HOME . DS. 'vendor/autoload.php';
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
            $mpdf->WriteHTML($messageBody);
            $mpdf->SetHTMLFooter('<hr style="margin: 0px 0px 0px 0px;" />
            <p style="text-align: center;font-size: small;">
            '.footeraddress($company['address']).' Tel.: '.$company['contact'].'<br />
            Email: account@fts-pl.com Website: http://www.fts-pl.com
            </p>');
            if ($proformaSwitch){ $mpdf->Output('pdf/proforma_'.$invoice['invoice_no'].'.pdf', 'F'); }
            else{  $mpdf->Output('pdf/invoice_'.$invoice['invoice_no'].'.pdf', 'F'); }
            echo 'chacha';
        }
    }
        
    function sendMail($invoice, $customer) {
        
        $sentMailTo = array();
        $sentMailTo = FXD_EMAIL_IDS;
        $sentMailTo[] = $customer['email'];
        
        try {
            $mailer = $this->_utils->getMailer();
            $message = (new Swift_Message("Invoice copy #$invoice[invoice_no] against PO $invoice[po_no]" ))
            ->setContentType("text/html")
            ->setFrom([HD_MAIL_ID => HD_NAME])
            ->setTo($sentMailTo)
            ->setBcc(FXD_EMAIL_IDS)
            ->setBody("Hi Sir/Mam, <br><br> PFA invoice <br><br><br><br> Regards,<br>Account")
            ->attach(
                Swift_Attachment::fromPath('./pdf/invoice_'.$invoice['invoice_no'].'.pdf')->setContentType('application/pdf')
                );
            
            // Send the message
            $result = $mailer->send($message);
            
            echo $result;
        } catch (Exception $e) {
            
            print_r($e->getMessage());
            
        }
    }
   
    public function getDetails($invoiceId) {
        $invoice = $this->_model->get($invoiceId);
        
        $paymentTbl = new PaymentsModel();
        $payments = $paymentTbl->getDetailsByInvoiceId($invoiceId);
        $paidAmount = 0;
        $payment = array();
        
        if(count($payments)) {
            foreach($payments as $row) {
                $paidAmount += $row['allocated_amt'];
                if(intval($row['tds_percent'])) {
                    $payment = $row;
                } 
            }
            if(empty($payment)) {
                $payment = $payments[0];
            }
            $payment['paid_amount'] = $paidAmount;
            $payment['balance_amt'] = $payment['receivable_amt'] - $paidAmount;
            unset($payment['allocated_amt']);
        }
        
        $invoice['payments'] = $payment;
        
        echo json_encode($invoice);
        
    }
    
    public function getInvoiceIdsByCustomer($custId) {
        $invoice = $this->_model->getInvoiceIdsByCustomer($custId);
        echo json_encode($invoice);
        
    }

    private function genInvoiceNo() {
        $newInvoiceNo = '';
        $prefix = date('Y');

        $lastRecord = $this->_model->getLastRecord();
        if($lastRecord) {
            $lastInvoiceNo = $lastRecord['invoice_no'];

            if(!empty($lastInvoiceNo)) {
                $inv = substr($lastInvoiceNo, -3);
                $prevprx = substr($lastInvoiceNo, 0, 4);

                $inv = ($inv + 1);
                if($prevprx != $prefix) {
                    $inv = '001';
                }
                $newInvoiceNo = $prefix.str_pad($inv, 3, 0, STR_PAD_LEFT);
            } else {
                $newInvoiceNo = $prefix.'001';
            }
        
        } else {
            $newInvoiceNo = $prefix.'001';
        }

        return $newInvoiceNo;



    }

    public function search() {

        $invoices = $this->_model->getList($_POST);
        
        $result = array(); 
        $result['draw'] = 1;
        $result['data'] = array();
        $result['recordsTotal'] = count($invoices);
        $result['recordsFiltered'] = count($invoices);

        foreach($invoices as $invoice) {
            $tmp = array();
            $tmp[] = $invoice['id'];
            $tmp[] = date('d, M Y',strtotime($invoice['invoice_date']));
            $tmp[] = $invoice['invoice_no'];
            $tmp[] = $invoice['po_no'];
            $tmp[] = $invoice['customer_name'];
            $tmp[] = $invoice['sales_person'];
            $tmp[] = $invoice['invoice_total'];
            $result['data'][] = $tmp;
        }

        

        echo json_encode($result);
        exit;
    }
    
    public function invoice_validty() {
        if(!empty($_POST)) {
             if($t = $this->_model->getRecordsByField('invoice_no', $_POST['invoice_no'])) {
                 echo 0;
             } else {
                echo true;
             }
        } else {
            echo false;
        }
    }

    public function proforma_validty() {
        if(!empty($_POST)) {
             if($t = $this->_model->proformaFieldRecord('invoice_no', $_POST['invoice_no'])) {
                 echo 0;
             } else {
                echo true;
             }
        } else {
            echo false;
        }
    }

    public function delete($invoiceNo) {
        $row = $this->_model->getByInvoiceNo($invoiceNo);

        if(!empty($row)) {
            $paymentTbl = new PaymentsModel();
            $payments = $paymentTbl->getDetailsByInvoiceId($row['id']);

            //print_r($payments); exit;

            if(empty($payments)) {
                $this->_model->deleteInvoice($invoiceNo);
                $invoiceItemTbl = new InvoiceItemsModel();
                $invoiceItemTbl->deleteByInvoiceId($row['id']);
                echo "<b><span style='color:green;'>invoice no. $invoiceNo deleted successfully!</span></b>";
            } else {
                echo "<b><span style='color:red;'>Record can't be deleted, have some payment records against invoice no. $invoiceNo.</span></b>";
            }
        } else {
            echo "<b><span style='color:red;'>No record for invoice no. $invoiceNo</span></b>";
        }


    }

    //einvoice 
    function getEinvoiceAuthToken($gst) {
        $url = EINVOICE_URL . 'eivital/dec/v1.04/auth?';
        $params = array('aspid' => ASP_ID, 'password' => EINVOICE_PASSWORD, 'user_name' => EINVOICE_USERNAME,'Gstin' => GST_NO, 'eInvPwd' => EINVPWD);
        $url = $url . http_build_query($params);
        $response = $this->sendRequest('GET', $url, $params);
        $data = json_decode($response, true);

        if($data['Status']) {
            return $data['Data']['AuthToken'];
        } else {
            return '';
        }
    }

    function postEinvoiceRequest($invoice, $dataItem, $customer) {

        $hsn = new HsnModel();
        $company = new CompanyModel();
        $company = $company->get(1);

        $authToken = $this->getEinvoiceAuthToken($company['gstin']);
        $url = EINVOICE_URL . 'eicore/dec/v1.03/Invoice?';
        $params = array('aspid' => ASP_ID, 'password' => EINVOICE_PASSWORD, 'user_name' => EINVOICE_USERNAME,'Gstin' => GST_NO, 'AuthToken' => $authToken);
        $url = $url . http_build_query($params);
        

        $request = array();
        $request['VERSION'] = '1.1';
        $request['TRANDTLS']['TAXSCH'] = 'GST';
        $request['TRANDTLS']['SUPTYP'] = 'B2B';
        $request['TRANDTLS']['REGREV'] = 'N';

        //Invoice no
        $request['DOCDTLS']['TYP'] = 'INV';
        $request['DOCDTLS']['NO'] = $invoice['invoice_no'];
        $request['DOCDTLS']['DT'] = date('d/m/Y', strtotime($invoice['invoice_date']));

        //FTSPL Details
        $company['gstin'] = '06AACCC1596Q002';
        $company['pincode'] = '122004';
        $request['SELLERDTLS']['GSTIN'] = $company['gstin'];
        $request['SELLERDTLS']['LGLNM'] = $company['name'];
        $request['SELLERDTLS']['TRDNM'] = $company['name'];
        $request['SELLERDTLS']['ADDR1'] = substr($company['address'], 0, 100);
        $request['SELLERDTLS']['ADDR2'] = null;
        $request['SELLERDTLS']['LOC'] = 'INDIA';
        $request['SELLERDTLS']['PIN'] = (int)$company['pincode'];
        $request['SELLERDTLS']['STCD'] = substr($company['gstin'], 0, 2);
        $request['SELLERDTLS']['PH'] = null;
        $request['SELLERDTLS']['EM'] = null;

        //Client Details
        $request['BUYERDTLS']['GSTIN'] = $customer['gstin'];
        $request['BUYERDTLS']['LGLNM'] = $customer['name'];
        $request['BUYERDTLS']['TRDNM'] = $customer['name'];
        $request['BUYERDTLS']['POS'] = substr($customer['gstin'], 0, 2);
        $request['BUYERDTLS']['ADDR1'] = substr($customer['address'], 0, 100);
        $request['BUYERDTLS']['ADDR2'] = null;
        $request['BUYERDTLS']['LOC'] = 'INDIA';
        $request['BUYERDTLS']['PIN'] = (int)$customer['pincode'];
        $request['BUYERDTLS']['STCD'] = substr($customer['gstin'], 0, 2);
        $request['BUYERDTLS']['PH'] = null;
        $request['BUYERDTLS']['EM'] = null;

        $request['DISPDTLS']['NM'] = $customer['gstin'];
        $request['DISPDTLS']['ADDR1'] = substr($customer['address'], 0, 100);
        $request['DISPDTLS']['ADDR2'] = null;
        $request['DISPDTLS']['LOC'] = 'INDIA';
        $request['DISPDTLS']['PIN'] = (int)$customer['pincode'];
        $request['DISPDTLS']['STCD'] = substr($customer['gstin'], 0, 2);

        //Item list
        $request['ITEMLIST'] = array();

        foreach($dataItem as $key => $item) {
            $tmp = array();
            $hsncode = $hsn->get($item['hsn_id']);
            $tmp['SLNO'] = (String)$key;
            $tmp['PRDDESC'] = $item['description'];
            $tmp['ISSERVC'] = 'N';
            $tmp['HSNCD'] = $hsncode['code'];
            $tmp['BARCDE'] = null;
            $tmp['QTY'] = (float)$item['qty'];
            $tmp['FREEQTY'] = 0;
            $tmp['UNIT'] = 'NOS';
            $tmp['UNITPRICE'] = (float)$item['unit_price'];
            $tmp['TOTAMT'] = (float)$item['total'];
            $tmp['DISCOUNT'] = 0;
            $tmp['PRETAXVAL'] = 0;
            $tmp['ASSAMT'] = (float)$item['total'];
            
            if($invoice['igst']) {
                $tmp['GSTRT'] = 18;
                $tmp['IGSTAMT'] = ($item['total'] * $tmp['GSTRT']) / 100;
                $tmp['CGSTAMT'] = 0;
                $tmp['SGSTAMT'] = 0;    
            } else {
                $tmp['GSTRT'] = 9;
                $tmp['IGSTAMT'] = 0;
                $tmp['CGSTAMT'] = ($item['total'] * $tmp['GSTRT']) / 100;
                $tmp['SGSTAMT'] = ($item['total'] * $tmp['GSTRT']) / 100;    
            }
            $tmp['CESRT'] = 0;
            $tmp['CESAMT'] = 0;
            $tmp['CESNONADVLAMT'] = 0;
            $tmp['STATECESRT'] = 0;
            $tmp['STATECESAMT'] = 0;
            $tmp['STATECESNONADVLAMT'] = 0;
            $tmp['OTHCHRG'] = 0;
            $tmp['TOTITEMVAL'] = $item['total'] + $tmp['IGSTAMT'] + $tmp['CGSTAMT'] + $tmp['SGSTAMT'];
            $tmp['ORDLINEREF'] = null;
            $tmp['ORGCNTRY'] = null;

            $request['ITEMLIST'][] = $tmp;
        }

        //Value detail
        $request['VALDTLS']['ASSVAL'] = (float)$invoice['sub_total'];
        $request['VALDTLS']['CGSTVAL'] = (float)$invoice['cgst'];
        $request['VALDTLS']['SGSTVAL'] = (float)$invoice['sgst'];
        $request['VALDTLS']['IGSTVAL'] = (float)$invoice['igst'];
        $request['VALDTLS']['CESVAL'] = 0;
        $request['VALDTLS']['STCESVAL'] = 0;
        $request['VALDTLS']['RNDOFFAMT'] = 0;
        $request['VALDTLS']['TOTINVVAL'] = (float)$invoice['invoice_total'];
        $request['VALDTLS']['TOTINVVALFC'] = (float)$invoice['invoice_total'];

        $request['EXPDTLS']['SHIPBNO'] = null;
        $request['EXPDTLS']['SHIPBDT'] = null;
        $request['EXPDTLS']['PORT'] = null;
        $request['EXPDTLS']['REFCLM'] = null;
        $request['EXPDTLS']['FORCUR'] = null;
        $request['EXPDTLS']['CNTCODE'] = null;
        $request['EXPDTLS']['EXPDUTY'] = 0;

        $request['EWBDTLS']['TRANSID'] = null;
        $request['EWBDTLS']['TRANSNAME'] = null;
        $request['EWBDTLS']['TRANSMODE'] = null;
        $request['EWBDTLS']['DISTANCE'] = 0;
        $request['EWBDTLS']['TRANSDOCNO'] = null;
        $request['EWBDTLS']['TRANSDOCDT'] = null;
        $request['EWBDTLS']['VEHNO'] = null;
        $request['EWBDTLS']['VEHTYPE'] = null;


        //echo '<pre>'; print_r($request); exit;

        $response = $this->sendRequest('POST', $url, $request);
        echo '<pre>'; print_r(json_decode($response, true)); exit;

    }

    function sendRequest($method, $url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
            )
        );
                
        if( strtoupper($method) == "POST" )
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $info =curl_errno($ch)>0 ? array("curl_error_".curl_errno($ch)=>curl_error($ch)) : curl_getinfo($ch);
        //print_r($response);
        curl_close($ch);
        return $response;
    }

}
// Jthayil Start
function getdeclaration($val, $flag=false) {
    if ($flag == false){
        if ($val != ""){ return "<tr><td colspan='6'><b>Declaration</b><br>". $val."</td></tr>"; }
        else{ return ""; }
    }
    else{if ($val != "") { return true; }
    else { return false; }}
}

function addressmaker($val, $firstLineBreak=2) {
    $pieces = explode(",", $val);
    $maxlen = 0;
    $skippiece = 0;
    $jar = "";
    // echo '<pre>';print_r($pieces); print_r(count($pieces));
    for ($x = 0; $x < count($pieces); $x++) {
        if ($x <= $firstLineBreak) {
            // print_r("0.".$x);
            $jar = $jar . $pieces[$x] . ", ";
            if ($x == $firstLineBreak) {$maxlen = strlen($jar);$jar = $jar ."<br>";}
        } else if ($x == $skippiece){
            // print_r(" 1.".$x);
            $skippiece = 0;
        } else if ($x == count($pieces)-1){
            // print_r(" 2.".$x);
            $jar = $jar . $pieces[$x];
        } else {
            // print_r(" 3.".$x);
            if ($x+1 <= count($pieces)-1 && strlen($pieces[$x]) + strlen($pieces[$x+1]) <= $maxlen + 3){
                if ($x+1 == count($pieces)-1){
                    $jar = $jar . $pieces[$x] . ", " . $pieces[$x+1] . ".";
                } else {
                    $jar = $jar . $pieces[$x] . ", " . $pieces[$x+1] . ",<br>";
                }
                $skippiece = $x+1;
            } else {
                $jar = $jar . $pieces[$x] . ",<br>";
            }
        }
    }
    // print_r($jar);
    return $jar;
}

function footeraddress($val) {
    $pieces = explode(",", $val);
    $maxlen = 0;
    $skippiece = 0;
    $jar = "";
    for ($x = 0; $x < count($pieces); $x++) {
        if ($x <= 3) {
            $jar = $jar . $pieces[$x] . ", ";
            if ($x == 3) {$maxlen = strlen($jar);$jar = $jar ."<br>";}
        } else if ($x == $skippiece){
            $skippiece = 0;
        } else if ($x == count($pieces)-1){
            $jar = $jar . $pieces[$x];
        } else {
            if ($x+1 <= count($pieces)-1 && strlen($pieces[$x]) + strlen($pieces[$x+1]) <= $maxlen + 3){
                if ($x+1 == count($pieces)-1){
                    $jar = $jar . $pieces[$x] . ", " . $pieces[$x+1] . ".";
                } else {
                    $jar = $jar . $pieces[$x] . ", " . $pieces[$x+1] . ",<br>";
                }
                $skippiece = $x+1;
            } else {
                $jar = $jar . $pieces[$x] . ",<br>";
            }
        }
    }
    return $jar;
}
// End