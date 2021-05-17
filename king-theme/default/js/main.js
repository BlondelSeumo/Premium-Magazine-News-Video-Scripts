$(document).ready(function() {

    /**
     * Search.
     */
    $(".search-toggle").click(function(event) {
        $("div.king-search").find('.king-search-field').focus();
    });
    /**
     * tooltip.
     */
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });
 
    /**
     * Copy Url.
     */
    $('#modal-url').click(function() {
        $(this).focus();
        $(this).select();
        document.execCommand('copy');
        $(this).next('.copied').show();
    });

});