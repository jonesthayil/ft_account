<body class="hold-transition sidebar-collapse layout-top-nav">
  <div class="wrapper">
    <div class="content-wrapper">
      <?php include HOME . DS . 'includes' . DS . 'menu.inc.php'; ?>
      <section class="content">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="card card-default mt-3">
                <div class="card-header">
                  <h3 class="card-title" style="line-height: 2.2">
                    View Customer
                  </h3>
                  <div class="text-right">
                    <a
                      href="<?php echo ROOT; ?>customers/edit/<?php echo $customer['id'] ?>"
                      class="btn btn-primary btn-sm"
                    >
                      Edit
                    </a>
                    <a
                      href="<?php echo ROOT; ?>customers"
                      class="btn btn-default btn-sm"
                    >
                      Back
                    </a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_customername"> Customer Name </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['name'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_contactperson"> Contact Person </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['contact_person'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_address"> Address </label>
                    </div>
                    <div
                      class="col-sm-12 col-lg-3 form-group"
                      style="text-align: justify"
                    >
                      <?php echo $customer['address'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_pincode"> Pincode </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['pincode'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_address"> State </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $state['name'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_gst"> PAN No. </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['pan'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_gst"> GSTIN </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['gstin'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_pphone"> Phone (primary) </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group numberonly">
                      <?php echo $customer['pphone'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_sphone"> Phone (alternative) </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group numberonly">
                      <?php echo $customer['aphone'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_fax"> Fax </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group numberonly">
                      <?php echo $customer['fax'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_email"> Email </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['email'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_addinfo"> Additional Info </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['remark'] ?>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-right">
                  <a
                    href="<?php echo ROOT; ?>customers/edit/<?php echo $customer['id'] ?>"
                    class="btn btn-primary btn-sm"
                  >
                    Edit
                  </a>
                  <a
                    href="<?php echo ROOT; ?>customers"
                    class="btn btn-default btn-sm"
                  >
                    Back
                  </a>
                </div>
              </div>
            </div>
          </div>
          <br /><br />
        </div>
        <?php include HOME . DS . 'includes' . DS . 'footer.inc.php'; ?>
      </section>
    </div>
  </div>
