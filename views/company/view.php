<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php include HOME . DS . 'includes' . DS . 'menu.inc.php'; ?>
    <div class="content-wrapper">
      <section class="content">
        <div class="container-fluid pb-5">
          <div class="row my-3">
            <div class="col-12">
              <div class="card card-default">
                <div class="card-header">
                  <h3 class="card-title" style="line-height: 2.2">
                    Company Details
                  </h3>
                  <div class="text-right">
                    <a href="<?php echo ROOT; ?>company/edit/<?php echo $customer['id'] ?>"
                      class="btn btn-primary btn-sm">
                      Edit
                    </a>
                    <a href="<?php echo ROOT; ?>company" class="btn btn-default btn-sm">
                      Back
                    </a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_companyname"> <b>Company Name</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['name'] ?>
                    </div>
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_state"> <b>Bank Name</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['bank_name'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_contactperson"> <b>Contact Person</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['contact_person'] ?>
                    </div>
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_state"> <b>Account No.</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['account_no'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_contact"> <b>Contact</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group numberonly">
                      <?php echo $customer['contact'] ?>
                    </div>
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_state"> <b>IFSC</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['ifsc_code'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_mobile"> <b>Mobile</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group numberonly">
                      <?php echo $customer['mobile'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_fax"> <b>Fax</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group numberonly">
                      <?php echo $customer['fax'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_email"> <b>Email</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['email'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_gst"> <b>PAN No.</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['pan'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_gst"> <b>SAC</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['sac'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_gst"> <b>GSTIN</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['gstin'] ?>
                    </div>
                  </div>

                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_pincode"> <b>Pincode</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $customer['pincode'] ?>
                    </div>
                  </div>
                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_address"> <b>Address</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group" style="text-align: justify">
                      <?php echo $customer['address'] ?>
                    </div>
                  </div>

                  <div class="row mx-1">
                    <div class="col-sm-12 col-lg-2">
                      <label for="id_state"> <b>State</b> </label>
                    </div>
                    <div class="col-sm-12 col-lg-3 form-group">
                      <?php echo $state['name'] ?>
                    </div>
                  </div>

                </div>
                <div class="card-footer text-right">
                  <a href="<?php echo ROOT; ?>company/edit/<?php echo $customer['id'] ?>"
                    class="btn btn-primary btn-sm">
                    Edit
                  </a>
                  <a href="<?php echo ROOT; ?>company" class="btn btn-default btn-sm">
                    Back
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
  <?php include HOME . DS . 'includes' . DS . 'footer.inc.php'; ?>