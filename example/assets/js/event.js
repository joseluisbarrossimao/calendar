$(function () {
    $(document).on('keyup', '.date-input-js', function (e) {
        console.log(e.keyCode);
        if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) {
            var value = $(this).val(), newValue = '', mask = $(this).data('mask');
            for (var a = 0; a < value.length; a++) {
                if ($.isNumeric(mask[a]) === false) {
                    if (value[a] != mask[a]) {
                        newValue += mask[a];
                    }
                }
                newValue += value[a];
            }
            $(this).val(newValue);
        }
    });
    $(document).on('keyup', '.time-input-js', function (e) {
        if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) {
            var value = $(this).val(), newValue = '', mask = $(this).data('mask');
            for (var a = 0; a < value.length; a++) {
                if ($.isNumeric(mask[a]) === false) {
                    if (value[a] != mask[a]) {
                        newValue += mask[a];
                    }
                }
                newValue += value[a];
            }
            $(this).val(newValue);
        }
    });
    $(document).on('click', '.event-allDay', function () {
        var times = $('.time-input-js'), valid = $(this).children().attr('checked'), input = $(this).html();
        for (var a = 0; a < times.length; a++) {
            if (typeof valid !== 'undefined') {
                times.eq(a).prop('readonly', false);
            } else {
                times.eq(a).prop('readonly', true);
            }
        }
        if (typeof valid !== 'undefined') {
            input = input.substring(0, input.lastIndexOf('checked')) + '>Dia todo';
        } else {
            input = input.substring(0, input.lastIndexOf('>')) + ' checked="1">Dia todo';
        }
        $(this).html(input);
    });
    $(document).on('submit', '.event-form', function (e) {
    });
});