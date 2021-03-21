$(document).ready(function() {
    $('input, textarea').on('change', function(e) {
        const type = $(this).data('form')
        const value = $(this).val()
        const $elm = $(this)
        const max = $(this).data('max')
        const min = $(this).data('min')
        let state = 'null'
        switch (type) {
            case 'title':
                state = vText(value, min, max)
                    break
            case 'meta-desc':
                state = vText(value, 10, 165)
                    break;
            case 'chapo':
                state = vText(value, 0, 35)
                    break;
            default:
                    break;
        }
        if (state == 'is-invalid') {
            showHelper($elm)
        }
        $elm.addClass(state)
    })
    $('input, textarea').on('focus', function() {
        const $elm = $(this)

        $elm.removeClass('is-valid')
        $elm.removeClass('is-invalid')

        showHelper($elm)
    })
    $('input, textarea').on('keyup', function() {
        const $elm = $(this)
        if ($elm.hasClass('form-count')) {
            counter($elm)
        }
    })
    $('input, textarea').on('blur', function () {
        const type = $(this).data('form')
        const value = $(this).val()
        const $elm = $(this)
        let state = 'null'
        switch (type) {
            case 'title':
                state = vTitre(value)
                    break
            case 'meta-desc':
                state = vText(value, 10, 165)
                    break;
            case 'chapo':
                state = vText(value, 0, 35)
                    break;
            default:
                    break;
        }
        if (state == 'is-invalid') {
            showHelper($elm)
        } else {
            hideHelper($(this))
        }
        $elm.addClass(state)
    })
})

function vTitre(str) {
    const minLength = 3
    const maxLength = 80

    if (str.length >= minLength && str.length <= maxLength) {
        return 'is-valid'
    }

    return 'is-invalid'
}

function vText(str, min, max) {
    if (str.length >= min && str.length <= max) {
        return 'is-valid'
    }

    return 'is-invalid'
}

function counter(elm) {
    const dMin = elm.data('min')
    const dMax = elm.data('max')
    const helperId = elm.data('help')
    const value = elm.val()
    let state = 'success'
    let notState = 'danger'
    if (value.length < dMin || value.length > dMax) {
        state = 'danger'
        notState = 'success'
    }
    const $countHelper = '<span id="helpCount-' + helperId + '" class="badge badge-' + state + ' badge-counter">'+ value.length + '/' + dMax + '</span>'
    const $countHelperElm = $('#helpCount-'+helperId)

    if ($countHelperElm.length > 0) {
        console.log('deja present', $countHelperElm)
        if (!$countHelperElm.hasClass('badge-'+state)) {
            $countHelperElm.removeClass('badge-'+notState)
            $countHelperElm.addClass('badge-'+state)
        }

        $countHelperElm.html(value.length + '/' + dMax)

        return
    }
    console.log('ajout de countHelper', $countHelper, $(elm))
    $('#'+helperId).append($countHelper)


}

function showHelper(elm) {
    const helperid = elm.data('help')

    $('#'+helperid).fadeIn()

}

function hideHelper(elm) {
    const helperid = elm.data('help')

    $('#'+helperid).slideUp()
}