
var emptyPaymentTermIds = [], deleteid, old_orderid, oneTimeLastFill = false;
var sgst = 0, cgst = 0, igst = 0;
var orderid_list = [], last_orderid = 0;
var ptlist = [];

// Order Type Change
$(document).on("change", "#id_ordertype", function () {
  if ($(this).val()) {
    $(".order").show();
    if (old_orderid != $(this).val()) {
      $("#orderlist").empty();
      orderid_list = [];
      last_orderid = 0
      old_orderid = $(this).val();
      $("#add_item").trigger("click");
      if ($(this).val() == "2") {
        $("#add_item").hide();
        $("#order_item_header_qty").text("Payment Slab");
        $("#id_uom1").empty().append('<option value="3" selected>Percentage (%)</option>').val("3");
        // addprojecttable();
      } else {
        $("#order_item_header_qty").text("Qty.");
      }
      $('select.uom').each(function () {
        $(this).val("")
      });
    }
  } else {
    $(".order").hide();
  }
});


// On quantity Change
$(document).on("change", ".qty", function () {
  qtycal($(this).attr("id"), $(this).data("id"))
});


// Delete order item modal activator
$(document).on("click", "i.trash", function () {
  deleteid = $(this).data("id");
  $("#modelactivate").click();
});


// Delete order item on item modal submit
$(".killrow").click(function () {
  $("#" + deleteid).remove();
  if (deleteid == "pt" + deleteid) {
    ptlist = jQuery.grep(ptlist, function (b) {
      return b != id;
    });
    pttotal();
    $("#byemodal").click();
  } else {
    orderid_list = jQuery.grep(orderid_list, function (b) {
      return b != deleteid;
    });
    if (orderid_list.length < 1) {
      $("#add_item").trigger("click");
    }
    ttotal();
    $("#byemodal").click();
  }
});


// Add new order button click
$("#add_item").on("click", function () {
  if (orderid_list.length < 1) {
    last_orderid = 1
  } else {
    last_orderid += 1
  }
  addrow(last_orderid);
});


function qtycal(qtyid, id) {
  if (qtyid == "id_ptquantity" + id) {
    val = $("#" + qtyid).val();
    if ((val % 5) > 0) {
      $("#" + qtyid).val(parseInt(val) + (5 - (val % 5)));
    }
    if (val > 100) {
      $("#" + qtyid).val(100);
    }
    lastfill();
    paymentTermcollector(id);
  } else {
    if ($("#id_ordertype").val() != 2) {
      ordercollector(id);
    }
  }
}

function lastfill() {
  paymentTermTotal = 0
  emptyPaymentTermIds = []
  $.each(ptlist, function (index, value) {
    if ($("#id_ptquantity" + value).val()) {
      if ((ptlist.length - 1) == index) {
        // Always sets last payment slab to balance
        $("#id_ptquantity" + value).val(100 - paymentTermTotal);
      }
      paymentTermTotal += parseInt($("#id_ptquantity" + value).val())
    } else {
      emptyPaymentTermIds.push(value);
    }
  });

  if (oneTimeLastFill == false) {
    if (emptyPaymentTermIds.length == 1) {
      balanc = 100 - paymentTermTotal
      if (balanc < 0) {
        balanc = ""
      }
      $("#id_ptquantity" + emptyPaymentTermIds[0]).val(balanc);
      paymentTermcollector(emptyPaymentTermIds[0]);
      oneTimeLastFill = true
      paymentTermTotal = 100
    }
  }

  // if (paymentTermTotal > 100) {
  //   id = ptlist[ptlist.length - 1]
  //   extra = parseFloat($("#id_ptquantity" + ptlist.length).val()) - (paymentTermTotal - 100);
  //   $("#id_ptquantity" + id).val(parseInt(extra));
  //   paymentTermcollector(id);
  // }
}

// On Unit Price Change
$(document).on("change", ".unitprice", function () {
  unitpriceval = $(this).val();
  if ($(this).attr("id") == "id_ptunitprice" + $(this).data("id")) {
    paymentTermcollector($(this).data("id"));
  } else {
    $(this).val(parseFloat($(this).val()));
    ordercollector($(this).data("id"));
    $.each(ptlist, function (index, value) {
      $("#id_ptunitprice" + value).val(unitpriceval);
      paymentTermcollector(value);
    });
  }
});

$(document).on("change", ".uom", function () {
  ordercollector($(this).data('id'));
});



// Amount Representation
function humanamount(val) {
  var val = new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR'
  }).format(val);
  return val
}


// Each Order Item calculator
function ordercollector(id) {
  if ($("#id_quantity" + id).val()) {
    $("#id_quantity" + id).data('val', $("#id_quantity" + id).val());
  } else {
    $("#id_quantity" + id).data('val', 0);
  }
  if ($("#id_unitprice" + id).val()) {
    $("#id_unitprice" + id).data('val', $("#id_unitprice" + id).val());
  } else {
    $("#id_unitprice" + id).data('val', 0);
  }
  rowqty = $("#id_quantity" + id).data('val');
  rowunitprice = $("#id_unitprice" + id).data('val');
  rowuom = $("#id_uom" + id).val();
  subtotal = 0;
  if (rowqty && rowunitprice) {
    if ($("#id_ordertype").val() == 2) { subtotal = rowunitprice; }
    else if (rowuom == 3) { subtotal = rowunitprice * (rowqty / 100); }
    else { subtotal = rowunitprice * rowqty; }
    $("#total" + id).val(subtotal);
    $("#total" + id).data('val', subtotal);
    $("#id_total" + id).text(humanamount(parseFloat(subtotal).toFixed(2)));
    ttotal()
  }
}

// Each Payment Term calculator
function paymentTermcollector(id) {
  if ($("#id_ptquantity" + id).val()) {
    $("#id_ptquantity" + id).data('val', $("#id_ptquantity" + id).val());
  } else {
    $("#id_ptquantity" + id).data('val', 0);
  }
  if ($("#id_ptunitprice" + id).val()) {
    $("#id_ptunitprice" + id).data('val', $("#id_ptunitprice" + id).val());
  } else {
    $("#id_ptunitprice" + id).data('val', 0);
  }
  rowqty = $("#id_ptquantity" + id).data('val');
  rowunitprice = $("#id_ptunitprice" + id).data('val');
  subtotal = 0;
  if (rowqty && rowunitprice) {
    subtotal = rowunitprice * (rowqty / 100);
    $("#pttotal" + id).val(subtotal);
    $("#id_pttotal" + id).text(parseFloat(subtotal).toFixed(2));
    pttotal();
  }
}

// All Payment Term Total calculator
function pttotal() {
  var days = 0, total = 0.0;
  if (ptlist != "") {
    $.each(ptlist, function (index, value) {
      qty = parseInt($("#id_ptquantity" + value).data('val'));
      subtotal = parseInt($("#pttotal" + value).data('val'));
      days += qty;
      total += subtotal;
    });
    $("#id_pttotaldays").val(days);
    $("#id_pttotal").val(total);
    $("#totalday").text(days);
    $("#pttotalvalue").text(humanamount(total));
  }
}

// All Order Items calculator
function ttotal() {
  subtotal = 0;
  if (orderid_list != "") {
    $.each(orderid_list, function (index, value) {
      subtotal += parseFloat($("#total" + value).data('val'));
    });
    $("#id_ordersubtotal").val(subtotal);
    $("#subtotal").text(humanamount(parseFloat(subtotal).toFixed(2)));
    igstval = igst / 100 * subtotal
    cgstval = cgst / 100 * subtotal
    sgstval = sgst / 100 * subtotal
    updateigst(igstval)
    updatecgst(cgstval)
    updatesgst(sgstval)
    gst = igstval + cgstval + sgstval
    total = gst + subtotal
    $("#id_ordertotal").val(total);
    $("#total").text(humanamount(parseFloat(total).toFixed(2)));
  }
}

function updateigst(val) {
  $("#id_igst").val(val);
  $("#igstvalue").text(humanamount(parseFloat(igstval).toFixed(2)));
}

function updatecgst(val) {
  $("#id_cgst").val(cgstval);
  $("#cgstvalue").text(humanamount(parseFloat(cgstval).toFixed(2)));
}

function updatesgst(val) {
  $("#id_sgst").val(sgstval);
  $("#sgstvalue").text(humanamount(parseFloat(sgstval).toFixed(2)));
}


// Customer Details Colletctor
function getcustomerdetails(customerid) {
  resetonbillto();
  if (customerid) {
    $.ajax({
      type: "POST",
      url: baseUrl + "customers/getdetails/" + customerid,
      data: customerid,
      dataType: "json",
      encode: true,
    })
      .done(function (data) {
        $("#salesperson_id").val(data.contact_person).removeClass("is-invalid");
        getgst(customerid);
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        alert("No details found against this customer.");
      });
  }
}

function getgst(customerid) {
  $.ajax({
    type: "POST",
    url: baseUrl + "invoices/gettaxesrate/" + customerid,
    dataType: "json",
    encode: true,
  })
    .done(function (data) {
      if (data.state == "same") {
        $("#sgstpercent").text(data.sgst);
        $("#sgstdiv").show();
        sgst = data.sgst;

        $("#cgstpercent").text(data.cgst);
        $("#cgstdiv").show();
        cgst = data.cgst;

        $("#igstdiv").hide();
        igst = 0;
        $("#id_taxrate").val(data.cgst);
      } else {
        $("#sgstdiv").hide();
        $("#cgstdiv").hide();
        $("#id_taxrate").val(data.igst);
        $("#igstpercent").text(data.igst);
        $("#igstdiv").show();
        cgst = 0;
        sgst = 0;
        igst = data.igst;
      }
      ttotal();
    });
}

function addprojecttable() {
  //projectdiv()
  //projecttablebody(1)
}


$(document).on("input propertychange paste", '#id_quantity1', function () {
  if ($("#id_ordertype").val() == 2) {
    ptlist = []
    $("#id_project").empty();
    $("#id_projectsummary").empty();
    $("#add_pt").hide();
    projectdiv()
    for (i = 0; i < $(this).val(); i++) {
      projecttablebody((i + 1));
    }
  }
});

// ================================== Supportive Functions ==================================


// Order Row creating function with row id as arguement
function addrow(id) {
  $("#orderlist").append("<tr id='" + id + "'></tr>");
  $("#" + id).append("<td class='form-group'><input type='text' class='form-control item' name='item[]' data-id='" + id + "' id='id_item" + id + "' placeholder='*Enter Name' /></td>")
    .append("<td class='form-group'><input type='text' class='form-control min150 desp' name='description[]' data-id='" + id + "' id='id_description" + id + "' placeholder='*Enter Description' /></td>")
    .append("<td class='form-group max150'><input type='number' class='form-control qty' data-qty='0' name='qty[]' data-val='0' data-id='" + id + "' id='id_quantity" + id + "' min='1' step='1' onkeypress='return event.charCode >= 48 && event.charCode <= 57' /></td>")
    .append('<td class="form-group"><select class="form-control uom" name="uom[]" data-id="' + id + '" id="id_uom' + id + '"><option value=""></option><option value="1">Day(s)</option><option value="2">AU</option><option value="3">Percentage (%)</option><option value="4">PC</option></select></td>')
    .append("<td class='form-group max150'><input type='number' class='form-control unitprice' data-up='0' name='unit_price[]' data-val='0' data-id='" + id + "' min='1' id='id_unitprice" + id + "' /></td>")
    .append("<td class='form-group pt-4'><input type='hidden' class='form-control rowtotal' data-total='0' value='' name='total[]' data-val='0' data-id='" + id + "' id='total" + id +
      "' ><span id='id_total" + id + "' >₹0.00</span></td>")
    .append("<td class='pt-4'><i class='fas fa-minus-circle trash' data-id='" + id + "' style='color: red' ></i></td>");
  orderid_list.push(id);
}

function projectdiv() {
  $("#id_project").append('<table class="table text-center mb-0" id="projectable"></table>');
  $("#projectable").append('<thead><tr id="projecttableheader"></tr></thead>')
    .append('<tbody id="projecttablebody"></tbody>');
  $("#projecttableheader").append('<th class="min100">Item</th>')
    .append('<th class="min100">Payment Term</th>')
    .append('<th class="minmax150">Payment Percent</th>')
    .append('<th class="minmax150">UOM</th>')
    .append('<th class="min100">Unit Price</th>')
    .append('<th class="min100">Total</th>');
  // $("#projecttableheader").append('<th class="min100">Delete</th>');
  $("#id_projectsummary").append('<hr class="mt-0"> <div class="row" id="ptsummary"> <div class="col-10 mb-2">    <button type="button" id="add_pt" class="btn btn-primary btn-sm">Add Payment Terms</button></div> <div class="col-2 mb-2">      <div class="row"> <div class="col-12 text-left"> <input type="hidden" name="pttotaldays" id="id_pttotaldays"  value="0"><b>Qty. : &nbsp; &nbsp; &nbsp; &nbsp;</b><span id="totalday">0</span></div> <div class="col-12 text-left" id="pttotaldiv"> <input type="hidden" name="ptsubtotal" id="id_pttotal" value="0.0" /><b>Total : &nbsp; &nbsp; &nbsp;</b><span id="pttotalvalue">0.00</span></div> </div> </div> </div>');
}

function projecttablebody(id) {
  $("#projecttablebody").append("<tr id='pt" + id + "'></tr>");
  $("#pt" + id).append("<td class='form-group'><input type='text' class='form-control item' name='ptitem[]' data-id='" + id + "' id='id_ptitem" + id + "' placeholder='Enter item name' /></td>")
    .append("<td class='form-group'><input type='text' class='form-control desp' data-id='" + id + "' name='paymentterm[]' id='id_paymentterm" + id + "' placeholder='Enter Description...' /></td>")
    .append("<td class='form-group max150'><input type='number' class='form-control qty'  value='' data-id='" + id + "' name='ptqty[]' id='id_ptquantity" + id + "' max='100' min='5' step='5' onkeypress='return event.charCode >= 48 && event.charCode <= 57' /></td>")
    .append('<td class="pt-3">Percentage (%)</td>')
    .append("<td class='form-group max150'><input type='number' class='form-control unitprice' name='ptunit_price[]' value='' data-id='" + id + "' id='id_ptunitprice" + id + "' /></td>")
    .append("<td class='form-group'><input type='hidden' class='form-control rowtotal' value='' name='pttotal[]' data-id='" + id + "' data-val='0' id='pttotal" + id + "' ><span id='id_pttotal" + id + "' >₹0.00</span></td>");
  // .append('<td><i class="fas fa-minus-circle trash" style="color: red" ></i></td>');
  ptlist.push(id);
  $("#id_ptunitprice" + id).val($("#id_unitprice1").val()).attr("readonly", true);
}
