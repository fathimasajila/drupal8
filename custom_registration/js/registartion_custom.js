jQuery(document).ready(function($){
    $('form#place-order-form').submit(function() {
        $(".alert").html('');
        // validate for any product 
        // pass on. For example:
        var count = $('input#count').val();
        var selected = 0;
        var qty = 1;
        for (id = 0; id <= count.length; id++) {
          //alert ($('input#item' + i).val());
          if( $('#item' + id).is(":checked")) {
              selected++;
              console.log(id);
              if ($('#qty' + id).val().length < 1) {
                qty = 0;  
                console.log(qty);
              }
          }
        }
        if (selected === 0) {
            //alert("Select any product");
            $(".alert").html("Select any product");
            return false;
        }
        if (qty === 0) {
            //alert("Please enter quantiy of selected product");
            $(".alert").html("Please enter quantiy of selected product");
            return false;
        }
    
    });
  
    $('[id^="edit-country"]').change(function() {
        var country = $('[id^="edit-country"]').val();
        $("[id^='edit-area']").find('option').remove();
        $.ajax({
            headers: {
            'Content-Type': 'application/hal+json',
            },
            method: 'POST',
            url: Drupal.url('area/list'), //"http://globalmedia.ae/npl/docroot/en/area/list",
            data: JSON.stringify({
            'country': country
        }),
        success: function (results) {
            var objJSON = $.parseJSON(results);
            $.each(objJSON, function (i,v)
            {
                $("[id = 'edit-area']").append(new Option(v, i));
            });
        }
       });
       $.ajax({
        headers: {
            'Content-Type': 'application/hal+json',
        },
        method: 'POST',
        url: Drupal.url('item/list'),//"http://globalmedia.ae/npl/docroot/en/item/list",
        data: JSON.stringify({
            'country': country
        }),
        success: function (results) {
            $(".product-section").html('');
            var objJSON = $.parseJSON(results);
            var i = 0;
            if (Object.keys(objJSON).length) {
                $(".product-section").append('<h4>Products</h4>');
                $('#edit-submit').show();
                $(".alert").html('');
                $.each(objJSON, function (id,values)
                {
                    var disc = (values[2]) ? values[2] : '';
                    var table = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody>';
                    table +=  '<tr><th scope="col">'  + values[1] + '</th><th scope="col"> Qty:</th></tr>';                
                    table +=  '<tr><td><label>'  + disc  + '</br></lable> <input id = "item' + i + '"  name = "item' + i + '" type="checkbox" value = '  + id + '><label>'  + values[3] + ', </label></td><td><div class="form-div"><input type="text" id = "qty' + i + '" name = "qty' + i + '"></div></td></tr></tbody></table>'; 
                    $(".product-section").append(table);
                        i++;
                    });
                $('#count').val(i);
            } else {
                //$("#edit-submit").hide();
            }    
        }
    });
    });
});

  