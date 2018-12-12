jQuery(document).ready(function($){
  $("#scs_widget_select_list").change(function(){

    var category;
    category = $("#scs_widget_select_list").val();

    // si l'option par defaut ("Choisissez une catégorie") est séléctionnée, ne rien faire
    if(category == -1) {
      return;
    }
    // $(".iboo-association-saved-label").show();
    // $(".iboo-association-saved-label").text('En cours d\'enregistrement ...');
    $(".loader").show();
    $.ajax({
        type: 'POST',
        url: ajax_object.ajax_url,
        data: {
            'action': 'get_products_by_category',
            'category' : category
        },
        success: function (response) {
          $("#saona-custom-search-results").html(response.data);
          $(".loader").hide();
          // $(".iboo-association-saved-label").text('Enregistré');
          // $(".iboo-association-saved-label").fadeOut(2000);
        },
        error: function( error) {
          console.log(error);
          $(".loader").hide();
        }
    });

  });

  $("#saona-custom-search-results").change(function(){
    var redirectUrl = $("#saona-custom-search-results").val();
    window.location.replace(redirectUrl);
  });

});
