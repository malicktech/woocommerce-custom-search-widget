jQuery(document).ready(function ($) {

  var allProducts = [];

  $("#saona-custom-search-results").ready(function () {

    if ( $('#saona-custom-search-results option').size() >= 2 ) { // not block ui when "browser back button clicked"
    $('#scs_widget_container').block({
      overlayCSS: {
        backgroundColor: '#a3a3a3'
      },
      baseZ: 2000,
      message: '<p>Chargement des produits ... </p>',
      css: {
        margin: '5px'
      }
    });
  }

  $.ajax({
    type: 'POST',
    url: ajax_object.ajax_url,
    data: {
      'action': 'get_all_products',
    },
    success: function (response) {
      allProducts = response.data;
      populateProductSelectList(allProducts);
      $('#scs_widget_container').unblock();
    },
    error: function (error) {
      console.log(error);
    }
  });
});

// when category is selected, load corresponding product
$("#scs_widget_select_list").change(function () {
  var category;
  category = $("#scs_widget_select_list").val();
  populateProductSelectListByCategory(category);
});

$('#saona-custom-search-results').on('change', function () {
  $('#scs-widget-search-button').prop('disabled', !$(this).val());
}).trigger('change');

function populateProductSelectList(products) {
  var options = '';
  for (var i = 0; i < products.length; i++) {
    options += '<option value="' + products[i][1] + '">' + products[i][0] + '</option>';
  }
  $("#scs-select-product-option").after(options);
}

function populateProductSelectListByCategory(category) {
  var options = '';
  for (var i = 0; i < allProducts.length; i++) {
    if ($.inArray(category, allProducts[i][2]) != -1) {
      options += '<option value="' + allProducts[i][1] + '">' + allProducts[i][0] + '</option>';
    }
  }
  $("#scs-select-product-option").nextAll().remove();
  $("#scs-select-product-option").after(options);
}

$("#scs-widget-search-button").click(function () {

  $('#scs_widget_container').block({
    overlayCSS: {
      backgroundColor: '#a3a3a3'
    },
    baseZ: 2000,
    message: '<p>Recherche disponibilité ... </p>',
    css: {
      margin: '5px'
    }
  });

  var category;
  var product;
  var redirectUrl = '';

  category = $("#scs_widget_select_list").val();
  product = $("#saona-custom-search-results").val();

  if (category == '' && product == '') {
    return;
  }

  if (category != '' && product == '') {
    redirectUrl = $("#scs_widget_select_list").find(':selected').attr('data-catlink');
  }

  if (product != '') {
    redirectUrl = $("#saona-custom-search-results").val();
  }

  window.location.href = redirectUrl;

});

});
