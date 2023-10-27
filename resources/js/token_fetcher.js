/* Siteimprove token.js */
import '../scss/token_fetcher.scss'

var $ = jQuery;

// Get request
$(document).on('click', '[data-siteimprove-token-generate]', function(e) {
    e.preventDefault();

    var $this = $(this),
        url = $this.data('siteimproveTokenGenerate');

    $.ajax({
        'url': url,
        'method': 'GET',
        'data': {},
        'beforeSend': function(){
            $this.attr({ disabled: true });
            $this.find('span').hide(0);
            $this.find('span').eq(1).show(0);
        }
    })

    .done(function(response) {
        $('#si-token-placeholder').addClass('token').val(response.token);
    })

    .fail(function(jqXHR, textStatus) {
        $('#si-token-placeholder').addClass('token').val(textStatus);
        $this.find('span').hide(0);
        $this.find('span:first').show(0);
        console.log(jqXHR, textStatus);
    })

    .always(function() {
        //$this.attr({ disabled: false });
        $this.find('span').hide(0);
        $this.find('span:last').show(0).css({ cursor: 'default' });
    });
});
