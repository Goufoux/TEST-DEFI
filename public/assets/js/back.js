$(document).ready(function () {
    $('.select2').select2({
        placeholder: 'Sélectionner une ou plusieurs rubrique(s)'
    })

    /* search product */

    $('#search_product').on('keyup', function (e) {
        const $elm = $(this)
        const value = $elm.val()
        if (value.length <= 2) {
            return
        }
        $.post({
            url: '/admin/product/search',
            data: 'name='+value,
            success: function (data) {
                if (false == data) {
                    printSearch(null)
                    return
                }
                const result = JSON.parse(data)
                printSearch(result)
            }
        })
    })

    /* multiple images */

    $('#addProductImage').on('click', function (e) {
        e.preventDefault()

        const nbFiles = $('.product-galleries').length

        const id = 'galleries_'+(nbFiles+1)
        const template = `
        <div class="product-galleries" id="productGallerieGroup-`+ (nbFiles+1) +`">
            <div class="row">
                <div class="form-group mb-3 col-lg-6 col-12">
                    <div class="custom-file">
                        <label class="label-galleries-`+ (nbFiles+1) +` custom-galleries-label custom-file-label" for="galleries_`+ (nbFiles+1) +`">Aucun fichier sélectionné</label>
                        <input type="file" name="galleries_`+ (nbFiles+1) +`" id="galleries_`+ (nbFiles+1) +`" class="hide custom-galleries custom-input-file">
                    </div>          
                </div>
                <div class="input-group mb-3 col-lg-6 col-12">
                    <div class="input-group-prepend">
                        <label for="alt_galleries_`+ (nbFiles+1) +`" class="input-group-text"><i class="fas fa-trash gallerie-delete-new ico ico-danger" data-target="productGallerieGroup-`+ (nbFiles+1) +`"></i>Balise alt</label>
                    </div>
                    <input type="text" name="alt_galleries_`+ (nbFiles+1) +`" maxlength="75" id="alt_galleries_`+ (nbFiles+1) +`" placeholder="Nom de l'image" class="form-control">
                </div>
            </div>
        </div>`

        $('.form-row-galleries').append(template)
    })

    $('.delete-gallerie-update').on('click', function () {
        const target = $(this).data('target')
        $.ajax({
            type: "GET",
            url: "/admin/product/imageLink/"+target,
            success: function (data) {
            }
        })
        $('#'+target).slideUp('slow', function () {
            $('#'+target).remove()
        })
    })

    $('.delete-image-actualite').on('click', function () {
        const target = $(this).data('target')
        $.ajax({
            type: "GET",
            url: "/admin/actualite/imageLink/"+target,
            success: function (data) {
            }
        })
        $('#actualite-'+target).slideUp('slow', function () {
            $('#alt').val("")
            $('#actualite-'+target).remove()
        })
    })

    $('.toggle-back-actu').on('change', function() {
        const target = $(this).data('target')
        const v = $(this).prop('checked') === true ? 1 : 0
        $.get({
            url: '/admin/actualite/update/homepage/' + target + '/' + v,
            success: function (data) {
                notifications()
            }
        })
    })

    $('.delete-image-product').on('click', function () {
        const target = $(this).data('target')
        $.ajax({
            type: "GET",
            url: "/admin/product/remove/image/"+target,
            success: function (data) {
                $('#alt').val(" ")
                $('#alt').removeAttr('required')
            }
        })
        $('#product-image-'+target).slideUp('slow', function () {
            $('#product-image-'+target).remove()
        })
    })

    // Draggable badge for image
    compteur = 0
    $badge = $('#drgbdg')
    if ($('#drgbdg').length > 0) {
        badgePos = $badge.position()
        badgePosY = badgePos.top
        badgePosX = badgePos.left
        $('#drgbdg').attr('data-oleft', badgePosX) 
        $('#drgbdg').attr('data-otop', badgePosY)
    }
    $('.badge-draggable').draggable({
        snap: ".snap-bloc",
        containment: ".main-drag",
        start: function () {
            $(this).animate({
                padding: 10+'px'
            }, 500)
        },
        drag: function () {
            $badge = $('#drgbdg')
            badgePos = $badge.position()
            badgePosY = badgePos.top
            badgePosX = badgePos.left 
            $('.snap-bloc').each(function (i, elm) {
                $elm = $(elm)
                elmHeight = $elm.height()
                elmWidth = $elm.width()
                pos = $elm.position()
                elmPosX = pos.left
                elmPosY = pos.top
                elmMaxY = elmPosY + elmHeight
                elmMaxX = elmPosX + elmWidth
                if (compteur <= 3) {
                    compteur++
                }
                if (badgePosX >= elmPosX && badgePosX <= elmMaxX) {
                    $elm.css('border', 'solid green 2px')
                } else {
                    $elm.css('border', 'none')
                }
            })
        },
        stop: function () {
            $(this).animate({
                padding: 3+'px'
            }, 500)
            $badge = $(this)
            badgePos = $badge.position()
            badgePosY = badgePos.top
            badgePosX = badgePos.left 
            $('.snap-bloc').each(function (i, elm) {
                $elm = $(elm)
                elmHeight = $elm.height()
                elmWidth = $elm.width()
                pos = $elm.position()
                elmPosX = pos.left
                elmPosY = pos.top
                elmMaxY = elmPosY + elmHeight
                elmMaxX = elmPosX + elmWidth
                if (badgePosX >= elmPosX && badgePosX <= elmMaxX) {
                    $elm = $(this)
                    $elm.css('border', 'solid green 2px')
                    target = $(this).data('target')
                    slug = 'gallery'
                    if ($elm.hasClass('product')) {
                        slug = 'product'
                    }
                    $.ajax({
                        type: 'GET',
                        url: '/admin/product/mev/'+slug+'/'+target,
                        success: function (data) {
                            if (data != 0) {
                                $('.snap-bloc').css('border', 'none')
                                data = JSON.parse(data)
                                $('.snap-bloc').each(function (i, elm) {
                                    $elm = $(elm)
                                    target = $elm.data('target')
                                    if (target == data.galleryId) {
                                        $(this).removeClass('gallerie').addClass('product')
                                        $(this).data('target', data.productId)
                                    }
                                    if (target == data.productId) {
                                        $(this).data('target', data.galleryId)
                                        $(this).removeClass('product').addClass('gallerie')

                                    }
                                })
                            }
                            notifications()
                        }
                    })
                } else {
                    $elm.css('border', 'none')
                }
            })
        }
    })
})

function printSearch(data) {
    const $container = $('#search-result .row')
    if (null === data) {
        $container.html("Aucun résultat pour cette recherche")
        return
    }
    console.log(data)
    $container.html("")
    $.each(data, function (i, v) {
        $container.append(`
            <div class="col-lg-3 col-md-4 col-sm-5 col-6 my-2">
                <div class="wrapper">
                    <h5 class="col-12">`+v.product_name+`</h5>
                    <div class="col-12">
                        <a href="/admin/product/`+ v.product_id +`/`+ v.product_slug +`">
                            <i class="fas fa-eye bg-dark p-2 text-white"></i>
                        </a>
                        <a href="/admin/product/update/`+ v.product_id +`">
                            <i class="fas fa-pen bg-dark p-2 text-white"></i>
                        </a>
                        <a href="/admin/product/remove/`+ v.product_id +`">
                            <i class="fas fa-trash bg-dark p-2 text-white"></i>
                        </a>
                    </div>
                </div>
            <img class="img-fluid" src="/upload/img/`+v.product_image+`">
        </div>`)
    })

}