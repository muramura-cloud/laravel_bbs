// sweetalert.jsを使用

// nodelistを取得
function setUpSingleDelete() {
    let admin_delete_forms = document.getElementsByName('admin_delete_form');
    let admin_delete_btns = document.getElementsByName("admin_delete_btn");

    // 個別削除
    for (var i = 0; i < admin_delete_btns.length; i++) {
        let admin_delete_form = admin_delete_forms[i];

        admin_delete_btns[i].addEventListener("click", function (e) {
            e.preventDefault();

            var options = {
                text: '本当に削除しますか？',
                buttons: {
                    ok: '削除する',
                    cancel: 'キャンセル',
                }
            };
            swal(options).then(function (value) {
                if (value) {
                    admin_delete_form.submit();
                }
            });

        }, false);
    }
}
setUpSingleDelete();

// 全選択ボタン
function setUpMultCheckBtn() {
    document.getElementById('all_check_btn').addEventListener('click', function () {
        let delete_checkboxes = document.getElementsByName('delete_checkbox');

        let check_count = 0;
        for (let i = 0; i < delete_checkboxes.length; i++) {
            if (delete_checkboxes[i].checked) {
                check_count++;
            }
        }

        if (check_count === delete_checkboxes.length) {
            for (let i = 0; i < delete_checkboxes.length; i++) {
                delete_checkboxes[i].checked = false;
            }
        } else {
            for (let i = 0; i < delete_checkboxes.length; i++) {
                delete_checkboxes[i].checked = true;
            }
        }
    });
}
setUpMultCheckBtn();


// まとめて削除
function setUpMultDeleteBtn() {
    document.getElementById('mult_delete_btn').addEventListener('click', function (e) {
        e.preventDefault();

        let delete_checkboxes = document.getElementsByName('delete_checkbox');

        let delete_ids = [];
        for (let i = 0; i < delete_checkboxes.length; i++) {
            if (delete_checkboxes[i].checked) {
                delete_ids.push(delete_checkboxes[i].value);
            }
        }

        if (delete_ids.length === 0) {
            swal('削除したいコンテンツにチェックを入れてください。');
            return;
        } else {
            var options = {
                text: '本当に削除しますか？',
                buttons: {
                    ok: '削除する',
                    cancel: 'キャンセル',
                }
            };
            swal(options).then(function (value) {
                if (value) {
                    document.getElementById('ids').value = delete_ids;
                    document.getElementById('admin_mult_delete_form').submit();
                }
            });
        }
    });
}
setUpMultDeleteBtn();

//検索
$('#admin_search_btn').on('click', function () {
    $('#posts_tbody').empty();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: '/admin/search/',
        data: {
            'title': $('#keyword_title').val(),
            'body': $('#keyword_body').val(),
            '_token': $('meta[name="csrf-token"]').attr('content'),
        },
        dataType: 'json',
    }).done(function (data) {
        console.log(data);

        let html = '<tbody id="posts_tbody">';

        $.each(data, function (index, value) {
            let id = value.id;
            let title = value.title;
            let body = value.body;
            let img = value.img;
            let has_comments = value.has_comments;
            let created_at = new Date(value.created_at).toLocaleString();
            let _token = value._token;

            // コメント存在するかどうかを確認する必要がる。それによって表示するコンテンツが変わる。
            let a_img = '画像なし';
            if (img) {
                a_img = '<a href="' + img + '"><img src="' + img + '" style = "width: 40px; height: 30px; " ></a > ';
            }

            let a_comment = 'コメント無し';
            if (has_comments) {
                a_comment = '<a href="/admin_comment/' + id + '" class="btn">コメント一覧へ</a > ';
            }

            html += `
            <tr>
                <th scope="row"><input type="checkbox" name="delete_checkbox" value="${id}"></th>
                <td>${id}</td>
                <td>${title}</td>
                <td>${body}</td>
                <td>${a_img}</td>
                <td>${a_comment}</td>
                <td>${created_at}</td>
                <td>
                    <form name="admin_delete_form" style="display: inline-block;" method="post" action="/admin_delete">
                        <input type="hidden" name="_token" value="${_token}">
                        <input type="hidden" name="post_id" value="${id}">
                        <button name="admin_delete_btn" class="btn btn-danger">削除</button>
                    </form>
                </td>
            </tr>
            `
        });

        html += '</tbody>';

        // console.log(html);

        $('#posts_table').append(html);

        // 削除ボタンなどのセットアップ
        setUpSingleDelete();
        setUpMultCheckBtn();
        setUpMultDeleteBtn();

        if (data.length === 0) {
            $('#posts_table').after('<p class="text-center mt-5 search-null">投稿が見つかりません</p>');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("ajax通信に失敗しました");
        console.log("jqXHR          : " + jqXHR.status); // HTTPステータスが取得
        console.log("textStatus     : " + textStatus);    // タイムアウト、パースエラー
        console.log("errorThrown    : " + errorThrown.message); // 例外情報
    });
});
