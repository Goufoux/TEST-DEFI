function notifications()
{
    // $.ajax({
    //     type: "GET",
    //     url: "/ajax/notifications",
    //     success: function(data) {
    //         let msg = JSON.parse(data)
    //         if(msg.length > 0) {
    //             msg.map(function(v, i) {
    //                 for (obj of v.messages) {
    //                     newToast(v.type, obj.title, obj.message)
    //                 }
    //             })
    //             $('#container-notifications').fadeIn('slow');
    //         }
    //     }
    // })
}

function newToast(type, title, message)
{
    let template
    template =  `<div class="toast bg-info" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
                    <div class="toast-header">
                        <strong>{{ title }}</strong>
                        <div class="float-right">
                            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="toast-body text-white">
                        {{ message }}
                    </div>
                </div>`;
    if (!title) {
        title = 'Message'
    }
    template = template
                .replace('{{ type }}', type)
                .replace('{{ title }}', title)
                .replace('{{ message }}', message)
    const $toast = $(template)
    $('#container-notifications').append($toast);
    $toast.toast('show')
    $toast.on('hidden.bs.toast', function () {
        this.remove()
        if ($('#container-notifications .toast').length <= 0) {
            $('#container-notifications').hide('slow');
        }
    })
    return;
}