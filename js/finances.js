$(document).ready(function(){
    get_installments();
});

function get_installments() {
    console.log("calling");
    $.ajax({                                      
        type: "GET",
        url: 'api/finances.php',
        data: "request=installments_list",
        dataType: 'json',
        success: function(response) {
            $(function() {
                var installments = []
                $.each(response, function(i, installment) {
                    console.log(installment);

                    var item = 
                    $('<a>').attr('class', 'list-group-item').append(
                        $('<span>').text(installment["shop_name"] + " (" + installment["description"] + ")  " + installment["full_amount"] + "kn" + "  " +  installment["payment_end"])).append(
                        $('<span>').attr('class', 'pull-right small').append(
                         $('<em>').text(installment[""]))).append(
                         
                          $('<span>').attr('class', 'pull-right small').append(
                              $('<em>').text(installment["paid_installments"] + " / " + installment["number_of_installments"]))
                      );
                      installments[i] = item;

                });
                $("#installments-list").html(installments);

            });
        } 
    });
};