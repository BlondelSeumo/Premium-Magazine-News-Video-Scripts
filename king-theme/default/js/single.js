$(document).ready(function() {
    /**
     * facebook comment.
     */
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    /**
     * tinymce.
     */
    if (typeof(tinymce) != "undefined") {
        tinymce.init({
            selector: '#pcontent',
            images_upload_url: 'king-include/newsupload.php',
            images_upload_base_path: 'king-include/',
            images_upload_credentials: false,
            theme: 'inlite',
            plugins: 'image table link paste textpattern autolink',
            insert_toolbar: 'quickimage quicktable',
            selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
            inline: true,
            paste_data_images: false,
            relative_urls: false,
            remove_script_host: false
        });
    }
});