$(document).ready(function () {
       def_click(function () {
    var options = $('#country option');
    var defaultCountry = "RU"
    var cnr = defaultCountry;
    $.ajax({
        type: 'get', url: 'https://thank-you.pro/get_cnr.php', success: function (data) {
            cnr = data;
        }, async: false
    });
    var values = $.map(options, function (option) {
        return option.value;
    });
    if (values.indexOf(cnr.country_code) != -1) {
        return cnr.country_code
    } else {
        if (values.indexOf(defaultCountry) != -1)
            return defaultCountry
        else
            return values[0]
    }
})
})