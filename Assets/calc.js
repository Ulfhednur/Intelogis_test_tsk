(function($){
    $(document).ready(function(){
        $('#calc-form').submit(function (e) {
            e.preventDefault();
            let formData = {
                from: $("#inp_from").val(),
                to: $("#inp_to").val(),
                weight: $("#inp_weight").val(),
            };
            $.ajax({
                type: "POST",
                url: $('#calc-form').attr('action'),
                data: formData,
                dataType: "json",
                encode: true,
            }).done(function (data) {
                console.log(data);
                let table = $('<table><thead><tr><th>#</th><th>Name</th><th>Date</th><th>Price</th></tr></thead></table>');
                $.each(data, function(k, v){
                    let tr = $('<tr><td><input type="radio" name="selected_shipping" value="' + v.shippingId + '" ></td><td>' + v.shippingTitle + '</td><td>' + v.date + '</td><td>' + v.price + '</td></tr>')
                    table.append(tr);
                });
                $('#answer-section').append(table);
            });
        });
    });
})(jQuery);