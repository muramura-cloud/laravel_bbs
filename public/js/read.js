$(function () {
    let details_btn = $('.details_btn');

    details_btn.on('click', function (e) {
        e.preventDefault();

        let $this = $(this);

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'GET',
            url: '/user/read/',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'post_id': $this.data('postid'),
                'page': $this.data('page'),
                'from': $this.data('from'),
            },
            dataType: 'json',
        }).done(function (data) {
            console.log(data);

            // 詳細ページへリダイレクト
            location.href = 'http://' + location.host + '/posts/' + data.post_id + '/?token=' + data._token + '/&page=' + data.page + '/&from=' + data.from;
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log("ajax通信に失敗しました");
            console.log("jqXHR          : " + jqXHR.status); // HTTPステータスが取得
            console.log("textStatus     : " + textStatus);    // タイムアウト、パースエラー
            console.log("errorThrown    : " + errorThrown.message); // 例外情報
        });
    });
});
