<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php include HOME . DS . 'includes' . DS . 'menu.inc.php'; ?>
    <div class="content-wrapper">
      <section class="content">
        <div class="container-fluid pb-5">
          <form action="" method="POST" id="id_quickForm" novalidate="novalidate">
            <div class="card card-default">
              <div class="card-header">
                <h3 class="card-title" style="line-height: 2.2">
                  Add New Payments
                </h3>
                <div class="text-right">
                  <button type="submit" class="btn btn-primary btn-sm vip" title="All fields are mandatory.">
                    Submit
                  </button>
                  <a href="<?php echo ROOT; ?>customers" class="btn btn-default btn-sm">
                    Back
                  </a>
                </div>
              </div>
              <div class="card-body p-3">
                <div class="row mx-1">
                  <div class="col-sm-12 col-lg-3 form-group">
                    <label for="id_customergroup">
                      Customer Group :
                    </label>
                    <select class="form-control" name="group_id" id="id_group_id">
                      <option value=""></option>
                      <?php foreach ($groups as $group) : ?>
                      <option value="<?php echo $group['id'] ?>">
                        <?php echo $group['name'] ?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-sm-12 col-lg-3 form-group">
                    <label for="customerid_id">Customer : </label>
                    <select class="form-control" name="customer_id" id="customerid_id" disabled>
                    </select>
                  </div>
                  <div class="col-sm-12 col-lg-3 form-group">
                    <label for="id_orderid">Customer PO No.:</label>
                    <select name="order_id" id="id_orderid" class="form-control" disabled></select>
                  </div>
                  <div class="col-12">
                    <label for="id_remarks">Remarks</label>
                    <textarea name="remarks" class="form-control capitalize" id="id_remarks" cols="30"
                      rows="1"></textarea>
                  </div>
                  <div class="row mx-1">
                    <div class="col-12">
                      <table class="table">
                        <thead>
                          <th>Payment Date </th>
                          <th>Cheque/UTR no.</th>
                          <th>Received Amt</th>
                          <!-- <th>Allocated Amt</th> -->
                        </thead>
                        <tbody>
                          <td class="form-group">
                            <input type="date" class="form-control www" name="payment_date" id="id_payment_date">
                          </td>
                          <td class="form-group">
                            <input type="text" class="form-control www" name="cheque_utr_no" id="id_cheque">
                          </td>
                          <td class="form-group">
                            <input type="number" class="form-control www rev" name="received_amt" min="1"
                              id="id_received_amt">
                          </td>
                        </tbody>
                      </table>
                    </div>
                    <div class="card-footer">
                      <div class="text-right">
                        <input type="hidden" class="form-control" name="balance_amt" id="id_balance_amt">
                        <b>Balance Amount : </b>
                        <span id="id_balanceamount"></span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12" id="new"></div>
                <div class="col-12" style="display: none;">
                  <button type="button" class="btn btn-default mr-2 addy add_row" data-row="1">
                    <i class="fas fa-plus" style="color: #007bff;"></i>&nbsp; Add New
                  </button>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary btn-sm vip" title="All fields are mandatory.">
                Submit
              </button>
              <a href="<?php echo ROOT; ?>customers" class="btn btn-default btn-sm">
                Back
              </a>
            </div>
        </div>
        </form>
    </div>
    </section>
  </div>
  <?php include HOME . DS . 'includes' . DS . 'footer.inc.php'; ?>