$(function () {
    console.log($(document).width(),$(document).height());
    var link, url = $(location).attr('href'), linkUser;
    if (url.lastIndexOf('?') != -1) {
        url = url.substring(0, url.lastIndexOf('?'));
    }
    for (var a = 0; a < $('.menu-js').length; a++) {
        if ($('.menu-js').eq(a).parent().hasClass('menu-active')) {
            link = $('.menu-js').eq(a).attr('href').split('/');
            break;
        }
    }
    for (var a = 0; a < $('.user').length; a++) {
        if ($('.user').eq(a).hasClass('user-acitve')) {
            link = $('.user').eq(a).children().attr('href').split('/');
        }
    }
    var data = {method: link[0], date: link[1], callback: {text: ''}};
    if (link.length > 2) {
        data = Object.assign(data, {user: link[3]});
    }
    $.ajax({
        method: "POST",
        url: url,
        data: data,
        datatype: 'json',
        success: function (response) {
            var resp = $.parseJSON(response);
            $(".schedule").html(resp.schedule);
            $('.calendar').html(resp.calendar);
            alinhaDivs($('.multi'));
            window.history.pushState(null, null, window.location.href);
        }
    });
    $(document).on('click', '.user-link', function (e) {
        e.preventDefault();
        var link = $(this).attr('href').split('/'), active = false, divs = $('.user'),
            lis = $('.menu ul li');
        for (var a = 0; a < divs.length; a++) {
            if (divs.eq(a).hasClass('user-active')) {
                divs.eq(a).removeClass('user-active');
            } else if (divs.eq(a).hasClass('user-not-active')) {
                divs.eq(a).removeClass('user-not-active');
            }
        }
        for (var a = 0; a < lis.length; a++) {
            var href = lis.eq(a).children().attr('href').split('/');
            if (href.length == 3) {
                delete href[2];
            }
            lis.eq(a).children().attr('href', href.join('/'));
        }
        if (link.join('/') == linkUser) {
            delete link[2];
            active = true;
        }
        if (!active) {
            $(this).parent().addClass('user-active');
            for (var a = 0; a < divs.length; a++) {
                if (divs.eq(a).children().attr('href') != link.join('/')) {
                    divs.eq(a).addClass('user-not-active');
                }
            }
            for (var a = 0; a < lis.length; a++) {
                var href = lis.eq(a).children().attr('href').split('/');
                if (href.length == 3) {
                    href[2] = link[2];
                } else {
                    href[2] = link[2];
                }
                lis.eq(a).children().attr('href', href.join('/'));
            }
        }
        var data = {method: link[0], date: link[1], callback: {text: ''}};
        if (link.length > 2) {
            data = Object.assign(data, {user: link[2]});
        }
        $.ajax({
            method: "POST",
            url: url,
            data: data,
            success: function (response) {
                console.log(response);
                var resp = $.parseJSON(response);
                $(".schedule").html(resp.schedule);
                $('.calendar').html(resp.calendar);
                alinhaDivs($('.multi'));
            }
        });
        linkUser = link.join('/');
    });

    $(document).on('click', '.event-js', function () {
        var time = $(this).children('.time-span').text();
        var data = {
            method: 'event',
            date: $(this).parent().children('.date-js-father').children('.date-js-children').text().split('/').reverse().join('-'),
            time: time.substring(time.indexOf(', ') + 2, time.indexOf(', ') + 7),
            id: $(this).children('.events-span').data('idquery'),
            dateDiff: $(this).children('.events-span').data('datediff'),
            timeDiff: $(this).children('.events-span').data('timediff'),
            subject: $(this).children('.events-span').text()
        };
        $.ajax({
            method: "POST",
            url: $(location).attr('href'),
            data: data,
            datatype: 'json',
            success: function (response) {
                showEvent(response);
            }
        });
    });
    $(document).on('click', '#modal', function (e) {
        if ((e.target.id == 'modal' ? e.target.id : e.currentTarget.firstElementChild.id) == 'event') {
            return false;
        }
        if ($(this).hasClass('show')) {
            showEvent('',false);
        }
    });
    $(document).on('click', '#close', function () {
        showEvent('',false);
    });
    $(document).on('change', '#choosePeriod', function () {
        $("#durationDisabled").show();
        if ($(this).val() == '') {
            $("#amountDisabled").hide();
            $("#methodDisabled").hide();
            $("#flowEdit").val('');
            $("#durationDisabled").hide();
            $('#choosePeriod').css({'width': '282px'});
        }
        if ($(this).val() == 'each') {
            $("#amountDisabled").show();
            $("#methodDisabled").show();
            $("#flowEdit").val('');
            $("#durationDisabled").css({'margin-left': '52%', 'width': '57%'});
            $('#choosePeriod').css({'width': '100px'});
        } else if ($(this).val() == 'every days') {
            $("#amountDisabled").hide();
            $("#methodDisabled").hide();
            $("#flowEdit").val('rum');
            $("#durationDisabled").css({'margin-left': '23%', 'width': '43.5%'});
            $('#choosePeriod').css({'width': '152px'});
        }
    });
    $(document).on('click', '#holidaysDisabled', function () {
        var value = ['.' + $(this).val(), '.moonPhases-js'], parent = $('.father');
        for (var a = 0; a < parent.length; a++) {
            if (parent.eq(a).children(value[0]).hasClass('disabled')) {
                if (parent.eq(a).hasClass('disabled')) {
                    parent.eq(a).removeClass('disabled');
                }
                parent.eq(a).children(value[0]).removeClass('disabled');
            } else {
                parent.eq(a).children(value[0]).addClass('disabled');
                if (parent.eq(a).children(value[1]).hasClass('disabled')) {
                    parent.eq(a).addClass('disabled');
                }
            }
        }
    });
    $(document).on('click', '#moonPhasesDisabled', function () {
        var value = ['.' + $(this).val(), '.holiday'], parent = $('.father');
        for (var a = 0; a < parent.length; a++) {
            if (parent.eq(a).children(value[0]).hasClass('disabled')) {
                if (parent.eq(a).hasClass('disabled')) {
                    parent.eq(a).removeClass('disabled');
                }
                parent.eq(a).children(value[0]).removeClass('disabled');
            } else {
                parent.eq(a).children(value[0]).addClass('disabled');
                if (parent.eq(a).children(value[1]).hasClass('disabled')) {
                    parent.eq(a).addClass('disabled');
                }
            }
        }
    });
    $(document).on('click', '#eventReset', function () {
        var data = {
            method: 'event',
            date: '',
            time: '',
            id: '',
            dateDiff: '',
            timeDiff: '',
            subject: ''
        };
        $.ajax({
            method: "POST",
            url: $(location).attr('href'),
            data: data,
            datatype: 'json',
            success: function (response) {
                showEvent(response);
            }
        });
    });
    $(document).on('click', '.callback', function (e) {
        var method = $(this).data('method'), date = $(this).data('callback'), data;
        e.preventDefault();
        if (method == 'day') {
            var days = $('.day-js');
            for (var a = 0; a < days.length; a++) {
                if (days.eq(a).children().attr('href') != $(this).attr('href')) {
                    days.eq(a).removeClass('span-select');
                } else {
                    days.eq(a).addClass('span-select');
                }
            }
            data = {
                method: link[0],
                date: link[1],
                callback: {
                    text: 'assembly display mode',
                    method: method,
                    date: date
                }
            };
        } else {
            window.history.pushState(null, null, window.location.href);
            data = {
                method: link[0],
                date: link[1],
                callback: {
                    text: 'assembly callback',
                    method: method,
                    date: date
                }
            };
        }
        if (link.length > 2) {
            data = Object.assign(data, {user: link[3]});
        }
        $.ajax({
            method: "POST",
            url: url,
            data: data,
            datatype: 'json',
            success: function (response) {
                var resp = $.parseJSON(response);
                if (method == 'day') {
                    $('.schedule').html(resp.data);
                } else {
                    $('.calendar').html(resp.data).css('height', resp.height);
                }
            }
        });
    });
    $(document).on('click', '.disabledLink', function (e) {
        e.preventDefault();
    });

    function alinhaDivs(multiEvent) {
        for (var a = 0; a < multiEvent.length; a++) {
            var parent = multiEvent.eq(a).parent(),
                eventSpan = parseFloat(parent.children('.events-span').eq(0).css('height')),
                height = eventSpan * parent.children('.events-span').length,
                marginBottom = parseFloat((parent.children('.events-span').eq(0).css('margin-bottom'))),
                marginsBottom = marginBottom * parent.children('.events-span').length;
            multiEvent.css({margin: parseInt(((height + marginsBottom) - (eventSpan + marginBottom)) / 2) + 'px 0px'});
            parent.css({height: parseInt((height + marginsBottom) - 3) + 'px'});
            parent.children('.events-span').eq(parent.children('.events-span').length - 1).css({'margin-bottom': '0rem;'});
        }
    }

    function showEvent(resp, show = true){
        var height=0,width=0;
        if(show) {
            width=(($(document).width()-891)/4)+'px';
            height=(($(document).height()-420)-30)+'px';
            $('#event').html(resp);
            $('#modal').addClass('show');
            $('#event').css('margin',width+' '+height);
        }else{
            width+='px';
            height+='px';
            $('#modal').children().html('');
            $('#modal').removeClass('show');
            $('#event').css('margin',width+' '+height);
        }
    }
});