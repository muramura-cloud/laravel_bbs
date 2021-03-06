let options = {
    text: '「イイね」をするにはログインしてください。',
    buttons: {
        ok: 'ログインページへ',
        cancel: 'キャンセル',
    }
};

$(function () {
    let like = $('.js_like_toggle');

    console.log(like);

    like.on('click', function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.data('user') === 'not_login') {
            swal(options).then(function (value) {
                if (value) {
                    location.href = 'http://' + location.host + '/login';
                }
            });

            return;
        }

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: '/ajaxlike',
            data: { 'post_id': $this.data('postid') },
            dataType: 'json',
        }).done(function (data) {
            $this.children('i').toggleClass('loved');
            $this.next('.likesCount').html(data.postLikesCount);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log("ajax通信に失敗しました");
            console.log("jqXHR          : " + jqXHR.status); // HTTPステータスが取得
            console.log("textStatus     : " + textStatus);    // タイムアウト、パースエラー
            console.log("errorThrown    : " + errorThrown.message); // 例外情報
        });
    });
});
