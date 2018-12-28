(function (OC, window, $, undefined) {
    'use strict';
    
    $(document).ready(function () {
        $.ajax({
            url: OC.generateUrl('/apps/mailadmin/domains'),
            method: 'GET'
        }).done(function (result) {
            console.log(result)
        }).fail(function (result) {
            console.warn(result)
        });
    });

    $(document).ready(function () {
        $.ajax({
            url: OC.generateUrl('/apps/mailadmin/domains/cezmatrix.sk'),
            //data: JSON.stringify({domain:"cezmatrix.sk"}),
            method: 'DELETE',
            contentType: 'application/json',
        }).done(function (result) {
            console.log(result)
        }).fail(function (result) {
            console.warn(result)
        });
    });
    
    })(OC, window, jQuery);