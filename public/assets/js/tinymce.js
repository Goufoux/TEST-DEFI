$(document).ready(function () {
    tinymce.init({
        selector: "#my-selector",
        plugins: "lists image table imagetools link media lists searchreplace fullscreen hr print preview anchor code save image emoticons directionality spellchecker",
        toolbar: "cut copy image table | gallery col-6 col-12 bloc-3-6-3 bloc-4-4-4 bloc-9-3 bloc-3-9 bloc-3*4 | undo redo | numlist bullist | styleselect searchplace formatselect link | forecolor backcolor | alignleft aligncenter alignright alignjustify | fullscreen | bold italic underline strikethrough fontsizeselect | preview ",
        language: 'fr_FR',
        browser_spellcheck: true,
        branding: false,
        block_formats: 'Paragraphe=p; Titre 1=h1; Titre 2=h2; Titre 3=h3; Titre 4=h4; Titre 5=h5;',
        force_br_newlines : true,
        force_p_newlines : true,
        remove_linebreaks: true,
        convert_newlines_to_brs : false,
        forced_root_block : '', // Needed for 3.x
        content_css: "/assets/css/index.css, /assets/css/tinymce.css",
        images_upload_url: '/tinymce/upload',
        relative_urls: false,
        setup: (editor) => {
            editor.ui.registry.addButton('col-6', {
                text: 'Bloc 6-6',
                tooltip: "Deux blocs de 6",
                onAction: () => {
                    editor.insertContent('&nbsp;<div class="row"><div class="col-lg-6 col-12 tinymce-col"><p>&nbsp;</p></div><div class="col-lg-6 col-12 tinymce-col"><p>&nbsp;</p></div></div>&nbsp;')
                }
            }),
            editor.ui.registry.addButton('bloc-3*4', {
                text: 'Bloc 3*4',
                tooltip: "4 blocs de 3",
                onAction: () => {
                    editor.insertContent('&nbsp;<div class="row"><div class="col-lg-3 col-md-6 col-12 tinymce-col"><p>&nbsp;</p></div><div class="col-lg-3 col-md-6 col-12 tinymce-col"><p>&nbsp;</p></div><div class="col-lg-3 col-md-6 col-12 tinymce-col"><p>&nbsp;</p></div><div class="col-lg-3 col-md-6 col-12 tinymce-col"><p>&nbsp;</p></div></div>&nbsp;')
                }
            }),
            editor.ui.registry.addButton('bloc-3-9', {
                text: 'Bloc 3-9',
                tooltip: "Un bloc de 3 et un bloc de 9",
                onAction: () => {
                    editor.insertContent('&nbsp;<div class="row"><div class="col-lg-3 col-12 tinymce-col"><p>&nbsp;</p></div><div class="col-lg-9 col-12 tinymce-col"><p>&nbsp;</p></div></div>&nbsp;')
                }
            }),
            editor.ui.registry.addButton('gallery', {
                text: 'Galleries',
                tooltip: "Bloc galleries",
                onAction: () => {
                    editor.insertContent('&nbsp;<div id="product-galleries">La gallerie s\'affichera ici!</div>&nbsp;')
                }
            }),
            editor.ui.registry.addButton('bloc-9-3', {
                text: 'Bloc 9-3',
                tooltip: "Un bloc de 9 - Un bloc de 3",
                onAction: () => {
                    editor.insertContent('&nbsp;<div class="row"><div class="col-lg-9 col-12 tinymce-col"><p>&nbsp;</p></div><div class="col-lg-3 col-12 tinymce-col"><p>&nbsp;</p></div></div>&nbsp;')
                }
            }),
            editor.ui.registry.addButton('col-12', {
                text: 'Bloc 12',
                tooltip: "Un bloc de 12",
                onAction: () => {
                    editor.insertContent('&nbsp;<div class="row"><div class="col-12 tinymce-col"><p>&nbsp;</p></div></div>&nbsp;')
                }
            }),
            editor.ui.registry.addButton('bloc-3-6-3', {
                text: 'Bloc 3-6-3',
                tooltip: "Un bloc de 3 - Un bloc de 6 - un bloc de 3",
                onAction: () => {
                    editor.insertContent('&nbsp;<div class="row"><div class="col-lg-3 col-md-4 col-12 tinymce-col"><p>&nbsp;</p></div><div class="col-lg-6 col-md-4 col-12 tinymce-col"><p>&nbsp;</p></div><div class="col-lg-3 col-md-4 col-12 tinymce-col"><p>&nbsp;</p></div></div>&nbsp;')
                }
            }),
            editor.ui.registry.addButton('bloc-4-4-4', {
                text: 'Bloc 4-4-4',
                tooltip: "Bloc de 4 - Bloc de 4 - Bloc de 4",
                onAction: () => {
                    editor.insertContent('&nbsp;<div class="row"><div class="col-lg-4 col-md-4 col-12 tinymce-col"><p>&nbsp;</p></div><div class="col-lg-4 col-md-4 col-12 tinymce-col"><p>&nbsp;</p></div><div class="col-lg-4 col-md-4 col-12 tinymce-col"><p>&nbsp;</p></div></div>&nbsp;')
                }
            })
        }
    })
    $btnNextForm = $('.btn-next-form')
    if ($btnNextForm.length > 0) {
        $btnNextForm.on('click', function (e) {
            e.preventDefault()
            if ($btnNextForm.hasClass('next')) {
                $('.form-hide').removeClass('d-none')
                $btnNextForm.removeClass('next')
                $btnNextForm.addClass('hide-form')
                $btnNextForm.html('Masquer formulaire')
            } else {
                $('.form-hide').addClass('d-none')
                $btnNextForm.addClass('next')
                $btnNextForm.removeClass('hide-form')
                $btnNextForm.html('Suivant')
            }
        })
    }
})