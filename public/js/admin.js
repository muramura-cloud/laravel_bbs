// sweetalert.jsを使用

// nodelistを取得
let admin_delete_forms = document.getElementsByName('admin_delete_form');
let admin_delete_btns = document.getElementsByName("admin_delete_btn");

// 個別削除
for (var i = 0; i < admin_delete_btns.length; i++) {
    let admin_delete_form = admin_delete_forms[i];

    admin_delete_btns[i].addEventListener("click", function (e) {
        e.preventDefault();


        var options = {
            text: '本当に投稿を削除しますか？',
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

// 全選択ボタン
document.getElementById('all_check_btn').addEventListener('click', function () {
    let delete_checkboxes = document.getElementsByName('delete_checkbox');

    for (let i = 0; i < delete_checkboxes.length; i++) {
        delete_checkboxes[i].checked = true;
    }
});

// まとめて削除
document.getElementById('mult_delete_btn').addEventListener('click', function (e) {
    e.preventDefault();

    let delete_checkboxes = document.getElementsByName('delete_checkbox');

    let delete_post_ids = [];
    for (let i = 0; i < delete_checkboxes.length; i++) {
        if (delete_checkboxes[i].checked) {
            // console.log(delete_checkboxes[i].value);
            delete_post_ids.push(delete_checkboxes[i].value);
        }
    }

    // 何もチェックされなかった場合はどうなる？
    if (delete_post_ids.length === 0) {
        swal('削除する投稿にチェックを入れてください。');
        return;
    } else {
        var options = {
            text: '本当に投稿を削除しますか？',
            buttons: {
                ok: '削除する',
                cancel: 'キャンセル',
            }
        };
        swal(options).then(function (value) {
            if (value) {
                document.getElementById('post_ids').value = delete_post_ids;
                document.getElementById('admin_mult_delete_form').submit();
            }
        });
    }
});

