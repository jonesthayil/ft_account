var baseUrl = window.location.origin + "/ft_account/";

$(function () {
  $(".select2").select2();
  // Form Validation
  $.validator.setDefaults({
    submitHandler: function () {
      form.submit();
    },
  });
  $("#quickForm").validate({
    rules: {
      customerid: {
        required: true,
      },
      invoicedate: {
        required: true,
        date: true,
      },
      payindays: {
        required: true,
      },
      shipby: {
        required: true,
      },
      shipcost: {
        required: true,
      },
      shiptax: {
        required: true,
      },
      tracking: {
        required: true,
      },
      customer: {
        required: true,
      },
      salesperson: {
        required: true,
      },
      bill_to: {
        required: true,
        textarea: true,
      },
      ship_to: {
        required: true,
        textarea: true,
      },
      remark: {
        required: true,
        textarea: true,
      },
      pcomment: {
        required: true,
        textarea: true,
      },
    },
    messages: {
      customerid: {
        required: "Please select this customer.",
      },
      orderid: {
        required: "Please select the order number.",
      },
      invoicedate: {
        required: "Please select a date.",
        date: "Value must be a date.",
      },
      payindays: {
        required: "Please enter days count.",
      },
      shipby: {
        required: "Please select Shipped By.",
      },
      shipcost: {
        required: "Please provide Ship cost.",
        tel: "Invalid Detail.",
      },
      tracking: {
        required: "Please provide tracking ref id.",
        tel: "Invalid Detail.",
      },
      customer: {
        required: "Please provide customer PO.",
        tel: "Invalid Detail.",
      },
      salesperson: {
        required: "Please provide Salesperson.",
      },
      bill_to: {
        required: "Please provide a Bill To",
      },
      ship_to: {
        required: "Please provide ship To.",
      },
      remark: {
        required: "Select enter a remark.",
      },
      pcomment: {
        required: "Please enter private remark.",
      },
    },
    errorElement: "span",
    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass("is-invalid");
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass("is-invalid");
    },
  });
  $("#add_item").click();
  fillorderitems();
});

$("#days_id").on("keypress", function (event) {
  var regex = new RegExp("^[0-9]+$");
  var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
  if (!regex.test(key)) {
    event.preventDefault();
    return false;
  }
});

// Dynamic Row Appending Function
function addrow(charlie) {
  $("#orderlist").append(
    "<tr id='" +
      charlie +
      "'><td><input type='number' class='form-control ftsm qty' min='1' step='1' onkeypress='return event.charCode >= 48 && event.charCode <= 57' name='qty[]' id='id_quantity" +
      charlie +
      "'/></td><td><input class='form-control ftsm' list='item" +
      charlie +
      "_list' name='item[]' id='id_item" +
      charlie +
      "' placeholder='Type or select...' /><datalist id='item" +
      charlie +
      "_list'><option value='a'></option><option value='b'></option></datalist></td><td><input class='form-control ftsm' list='description" +
      charlie +
      "_list' name='description[]' id='id_description" +
      charlie +
      "' placeholder='Type or select...' /> <datalist id='description" +
      charlie +
      "_list'><option value='a'></option><option value='b'></option></datalist></td><td><input type='number' class='form-control ftsm unitprice' name='unit_price[]' id='id_unitprice" +
      charlie +
      "'/></td><td><input type='number' class='form-control ftsm tax' name='tax[]' id='id_tax" +
      charlie +
      "'></td><td>₹<input type='hidden' class='form-control ftsm rowtotal'  value='0.00' name='total[]' id='total" +
      charlie +
      "'><span id='id_total" +
      charlie +
      "'>0.00</span></td><td><i class='fas fa-minus-circle trash' style='color: red' value='" +
      charlie +
      "'></i></td></tr>"
  );
}

// Delete Click Action
$(document).on("click", "i.trash", function () {
  $(".killrow").attr("id", $(this).attr("value"));
  $("#modelactivate").click();
});

// Delete & Return
$(".killrow").click(function () {
  var a = $(this).attr("id");
  $("#" + a).remove();
  var arr = $("#id_tr").val().split(",");
  res = jQuery.grep(arr, function (b) {
    return b !== a;
  });
  $("#id_tr").val(res);
  ttotal();
  $("#byemodal").click();
});

// Cancel delete action
// $(".order").click(function () {
//   $("#order").show();
//   $("#trash").hide();
// });

// Add Order Item Click Action
$("#add_item").on("click", function () {
  var a = $("#id_tr").val().split(",");
  console.log(a);
  if (a.length < 2 && a[0] == "") {
    addrow(1);
    a[0] = 1;
  } else {
    var lastid = a[a.length - 1];
    lastid++;
    addrow(lastid);
    a.push("" + lastid + "");
  }
  $("#id_tr").val(a);
  console.log($("#id_tr").val());
});

// Monitoring Tax Field
// $(".tax").change(function () {
//   ttotal();
// });

// Monitoring Quantity Field
$(document).on("change", ".qty", function () {
  var qtyid = $(this).attr("id");
  id = qtyid.match(/\d+/);
  subtotal = rowcollector(id[0]);
  $("#total" + id[0]).val(subtotal);
  $("#id_total" + id[0]).text(parseFloat(subtotal).toFixed(2));
  ttotal();
});

// Monitoring Unit Price Field
$(document).on("change", ".unitprice", function () {
  var unitpriceid = $(this).attr("id");
  id = unitpriceid.match(/\d+/);
  subtotal = rowcollector(id[0]);
  $("#total" + id[0]).val(subtotal);
  $("#id_total" + id[0]).text(parseFloat(subtotal).toFixed(2));
  ttotal();
});

// Monitoring Discount Field
$(document).on("change", ".discount", function () {
  var discountid = $(this).attr("id");
  id = discountid.match(/\d+/);
  subtotal = rowcollector(id[0]);
  $("#total" + id[0]).val(subtotal);
  $("#id_total" + id[0]).text(parseFloat(subtotal).toFixed(2));
  ttotal();
});

// Monitoring Tax Field
$(document).on("change", ".tax", function () {
  var taxid = $(this).attr("id");
  id = taxid.match(/\d+/);
  subtotal = rowcollector(id[0]);
  $("#total" + id[0]).val(subtotal);
  $("#id_total" + id[0]).text(parseFloat(subtotal).toFixed(2));
  ttotal();
});

// Calculation Sub Total & Total
function ttotal() {
  var idlist = $("#id_tr").val().split(",");
  total = 0;
  if (idlist != "") {
    $.each(idlist, function (index, value) {
      total += parseFloat($("#id_total" + value).text());
    });
    $("#id_ordertotal").val(total);
    $("#subtotal_id").text(parseFloat(total).toFixed(2));
    $("#total").text(parseFloat(total).toFixed(2));
  } else {
    total = 0.0;
    $("#id_ordertotal").val(total);
    $("#subtotal_id").text(parseFloat(total).toFixed(2));
    $("#total").text(parseFloat(total).toFixed(2));
  }
}

// Row Data Calculator
function rowcollector(id) {
  rowqty = $("#id_quantity" + id).val();
  rowunitprice = $("#id_unitprice" + id).val();
  rowtax = $("#id_tax" + id).val();
  rowdiscount = $("#id_discount" + id).val();
  total = 0;
  if (rowqty[0] != "" && rowunitprice[0] != "") {
    total = rowunitprice * rowqty;
    if (rowdiscount[0] != "") {
      total -= total * (rowdiscount / 100);
    }
    if (rowtax[0] != "") {
      total += total * (rowtax / 100);
    }
  }
  return total;
}

// Customer Ajax
$("#customerid_id").change(function () {
  var customerid = $(this).val();
  if (customerid) {
    $("#id_orderid").removeAttr("disabled");
    $.ajax({
      type: "POST",
      url: baseUrl + "orders/getOrderListByCustomer/" + customerid,
      dataType: "json",
      encode: true,
    })
      .done(function (data) {
        $.each(data, function (key, value) {
          $("#id_orderid").append(
            $("<option>", { value: value.id }).text(value.id)
          );
        });
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        alert("No details found against this customer.");
      });
  } else {
    $("#id_orderid")
      .find("option")
      .remove()
      .end()
      .append('<option value=""></option>')
      .val("");
  }
});

$("#id_orderid").change(function () {
  var orderId = $(this).val();
  if (orderId) {
    console.log(orderId);
    $.ajax({
      type: "POST",
      url: baseUrl + "orders/getdetails/" + orderId,
      dataType: "json",
      encode: true,
    })
      .done(function (data) {
        $("#id_payindays").val(data.order.pay_days);
        $("#id_pono").val(data.order.po_no);
        $("#id_salesperson").val(data.order.sales_person);
        $("#bill_id").val(data.order.bill_to);
        $("#ship_id").val(data.order.ship_to);

        $.each(data.items, function (key, value) {
          addrow(key);
        });
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        alert("No details found against this customer.");
      });
  } else {
  }
});

//orderid must be asc to desc
datadict = {
  20: { qt: 1, itm: "a", des: "b", up: 1, tax: 1 },
  21: { qt: 4, itm: "a", des: "b", up: 4, tax: 4 },
};
function fillorderitems(datadict) {
  var ttotal = 0
  var data = eval(datadict);
  for (var key in data) {
    if (data.hasOwnProperty(key)) {
      if (key != "") {
        var quantity = data[key].qt;
        var item = data[key].itm;
        var description = data[key].des;
        var unitprice = data[key].up;
        var tax = data[key].tax;
        var di = parseInt(key, 10);
        if (key == 1) {
          $("#id_quantity1").val(quantity);
          $("#id_item1").val(item);
          $("#id_description1").val(description);
          $("#id_unitprice1").val(unitprice);
          $("#id_tax1").val(tax);
        } else {
          $("#1").remove();
        }
        addrow(di);
        $("#id_quantity" + di).val(quantity);
        $("#id_item" + di).val(item);
        $("#id_description" + di).val(description);
        $("#id_unitprice" + di).val(unitprice);
        $("#id_tax" + di).val(tax);
        var total = 0;
        if (quantity != "" && unitprice != "") {
          total = unitprice * quantity;
          if (tax != "") {
            total += total * (tax / 100);
          }
        }
        $("#total" + di).val(total);
        $("#id_total" + di).text(parseFloat(total).toFixed(2));
      }
    }
    ttotal += total;
  }
  $("#id_ordertotal").val(ttotal);
  $("#subtotal_id").text(parseFloat(ttotal).toFixed(2));
  $("#total").text(parseFloat(ttotal).toFixed(2));
}

//{"order":{"id":"20","customer_id":"1","order_date":"2021-04-23 00:01:00","pay_days":"23","po_no":"123456","sales_person":"Mangesh","bill_to":"thane","ship_to":"thane","ordertotal":"17.65","remarks":"","status":"1","added_date":"2021-04-23 00:02:16","updated_date":"2021-04-23 00:02:16"},"items":false}
