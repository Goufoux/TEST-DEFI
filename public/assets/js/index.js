$(document).ready(function () {
    notifications()
    if ($('#index').length > 0) {
        $.get({
            url: '/dimension?height=' + screen.height + '&width=' + screen.width,
            success: function (data) {
            }
        })
    }
    
    
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
        $('.toast').toast({
            animation: true,
            autohide: true,
            delay: 5000
        })
    })

    // mise à niveau du contenu principal	

    $mainContent = $('#content_row')	
    const $leftColumn = $('#left-column')
    let lcH = $leftColumn.height()

    if (!$leftColumn.is(':visible') || $leftColumn.length <= 0) {
        lcH = 0
    }

    if ($mainContent.length > 0) {	
        let height = $mainContent.height()	
        const posX = $mainContent.posX	

        const $nav = $('#main-navbar')	
        const navH = $nav.outerHeight(true)

        const $headrow = $('#head-row')	
        const headrowH = $headrow.height()	
        
        
        const wh = window.innerHeight;	
        
        let replaceH = wh - (headrowH + navH + height)
        let missH = 0; 
        let posLcH = 0
        let posxLch = 0
        if ($leftColumn.length > 0) {
            posLcH = $leftColumn.position()
            posxLch = posLcH.top
        }
        if (height < lcH) {
            missH = lcH - height + 0
        }
        
        if (replaceH > 0 || (missH > 0 || posxLch > height)) {
            if (replaceH < 0) {
                replaceH = 0
            }
            mainContentHeight = height + replaceH + (missH - replaceH);	
            $mainContent.animate({	
                height: +mainContentHeight+'px'	
            }, 750, function() {
                replaceFooter()
            })	
        } else {
            replaceFooter()
        }
    }

    $(window).resize(function () {
        replaceFooter()
    })
    
    $('.carousel').carousel({
        interval: 5000
    })

    $('body').on('change', '.custom-input-file', function () {
        const $input = $(this)
        let id = $(this).attr('id')
        id = id.split('_')
        id = id[1]
        $label = $('.label-galleries-'+id)
        const fileName = $input.val().split('\\').pop()
        if ($input.hasClass('custom-galleries')) {
            $label.html(fileName)
        } else {
            $input.siblings('.custom-file-label').html(fileName)
        }
    
        const files = this.files
        if (files === undefined || files.length === 0) return
    
        // Retour si ce n'est pas une image
        const fileType = files[0]['type']
        const validImageTypes = ['image/gif', 'image/jpeg', 'image/png']
        if (!validImageTypes.includes(fileType)) {
            return
        }

        // Passe le champ alt en obligatoire
        const $altField = $('#alt')
        if ($altField.length > 0) {
            $altField.attr('required', 'required')
        }
    
        // Afficher un aperçu de l'image sélectionnée
        const reader = new FileReader()
        reader.onload = function (e) {
            const $parent = $input.parent().parent().parent()
            $parent.children('a').remove() // Première fois pour retirer le lien de téléchargement
            const $previewElParent = $('.preview-parent')
            const $previewEl = $('.preview-photo')
            if ($previewEl.length === 0) {
            $previewElParent.html('<div class="wrapper-previsualisation"><i class="fa fa-trash preview-delete"></i><img class="preview-photo img-fluid w-50" src="' + e.target.result + '" alt="prévisualisation de l\'image"></div>')
            } else {
            $previewEl.attr('src', e.target.result)
            }

            $('body').on('click', '.preview-delete', function () {
                $('#file').val("")
                $('.custom-file-label').html('Aucun fichier sélectionné')
                $altField.removeAttr('required')
                $altField.val("")
                $('.wrapper-previsualisation').hide('slow', function () {
                    $('.wrapper-previsualisation').remove()
                })
            })

        }
        reader.readAsDataURL(files[0])
        })
        $('body').on('click', '.gallerie-delete-new', function () {
            const target = $(this).data('target')
            $('#'+target).slideUp('slow', function () {
                $('#'+target).remove()
            })
        })

    // Menu mobile
    $containerMobile = $('#content-menu-mobile')

    $('body .container-fluid').on('click', function() {
        if ($containerMobile.is(':visible')) {
            $('.mob-sub-menu').slideUp()
            $containerMobile.fadeOut('slow')
        }
    })

    $('.toggler-mobile').on('click', function () {
        if ($containerMobile.is(':visible')) {
            $('.mob-sub-menu').slideUp()
            $containerMobile.fadeOut()
        } else {
            $containerMobile.fadeIn()
        }
    })

    $containerMobile.on('click', '.fa-times', function () {
        $('.mob-sub-menu').slideUp()
        $containerMobile.fadeOut()
    })

    $('.mob-menu').on('click', function () {
        $('.mob-sub-menu').slideUp()
        const $elm = $(this)
        const target = $elm.data('target')
        if ($('#'+target).is(':visible')) {
            $('#'+target).slideUp()
        } else {
            $('#'+target).slideDown()
        }
    })
})

function replaceFooter(origin = false)
{
    const body = document.body
    const wh = window.innerHeight
    const $refFooter = $('.ref-footer-div')
    const $leftColumn = $('body #left-column')
    
    let adjustHeight = $leftColumn.height() + 85

    if (adjustHeight > $refFooter.height()) {
        $refFooter.addClass('footer-fix')
    } else if ($refFooter.hasClass('footer-fix')) {
        $refFooter.removeClass('footer-fix')
    }

    $('#footer').animate({
        opacity: 1
    }, 750)
}
