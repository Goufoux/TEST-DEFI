$(document).ready(function () {

    $productSection = $('#product-section')
    productId = $productSection.data('product')

    $('#product-galleries').load('/tinymce/'+productId+'/galleries', function () {
        $('#product-galleries').animate({
            opacity: 1
        }, 500)
        addDrift()
        $mainDiapo = $('#diapo-main')
        $gallery = $('#diapo-gallery')

        if (detectmob()) {
            $('.diapo-elm').on('touchstart', function () {
                $elm = $(this)
                $imgGallery = $elm.children()
                $tempMainGallery = $mainDiapo.children()
                $mainDiapo.children().remove()
                $mainDiapo.append($imgGallery)
                $tempMainGallery.attr('id', '')
                $imgGallery.attr('id', 'g-zoom')
                $elm.append($tempMainGallery)
                addDrift()
            })
        } else {
            $('.diapo-elm').on('click', function () {
                $elm = $(this)
                $imgGallery = $elm.children()
                $tempMainGallery = $mainDiapo.children()
                $mainDiapo.children().remove()
                $mainDiapo.append($imgGallery)
                $tempMainGallery.attr('id', '')
                $imgGallery.attr('id', 'g-zoom')
                $elm.append($tempMainGallery)
                addDrift()
            })
        }
    })

})

function addDrift() {
    $(function() {
        $block = document.getElementById('product-galleries')
        pos = $block.getBoundingClientRect()
        $('.disable-button').click()
        var drift = new Drift(document.querySelector('#g-zoom'), {
            paneContainer: document.querySelector('.detail'),
            inlinePane: false,
            hoverBoundingBox: true,
            inlineOffsetY: -10,
            zoomFactor: 4,
            onShow: function () {
                if (pos.x > 400) {
                    $('.drift-zoom-pane').css('border', 'solid red 5px')
                }
            },
            hoverDelay: 150,
            containInline: false,
            touchBoundingBox: true,
            boundingBoxContainer: document.body,
            inlineContainer: document.querySelector('.detail')
        })
        document.querySelector(".disable-button").addEventListener("click", function() {
            drift.disable()
        });
    })
}

function detectmob() { 
    if( navigator.userAgent.match(/Android/i)
    || navigator.userAgent.match(/webOS/i)
    || navigator.userAgent.match(/iPhone/i)
    || navigator.userAgent.match(/iPad/i)
    || navigator.userAgent.match(/iPod/i)
    || navigator.userAgent.match(/BlackBerry/i)
    || navigator.userAgent.match(/Windows Phone/i)
    ){
       return true;
     }
    else {
       return false;
     }
}