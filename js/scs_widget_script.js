jQuery(document).ready(function($){

  var allProducts = [];

  $("#saona-custom-search-results").ready(function() {
    $.ajax({
        type: 'POST',
        url: ajax_object.ajax_url,
        data: {
            'action': 'get_all_products',
        },
        success: function (response) {
          allProducts = response.data;
          populateProductSelectList(allProducts);
          $("#saona-custom-search-results").removeAttr('disabled');
        },
        error: function( error) {
          console.log(error);
        }
    });
  });

  $("#scs_widget_select_list").change(function(){
    var category;
    category = $("#scs_widget_select_list").val();
    populateProductSelectListByCategory(category);

  });

  $("#saona-custom-search-results").change(function(){
    var redirectUrl = $("#saona-custom-search-results").val();
    window.location.href = redirectUrl;
  });

  function populateProductSelectList(products) {
    var options = '';
    for (var i = 0; i < products.length; i++) {
      options += '<option value="'+products[i][1]+'">'+products[i][0]+'</option>';
    }
    $("#scs-select-product-option").after(options);
  }

  function populateProductSelectListByCategory(category) {
    var options = '';
    for (var i = 0; i < allProducts.length; i++) {
      if($.inArray(category, allProducts[i][2]) != -1) {
        options += '<option value="'+allProducts[i][1]+'">'+allProducts[i][0]+'</option>';
      }
    }
    $("#scs-select-product-option").nextAll().remove();
    $("#scs-select-product-option").after(options);
  }

});
