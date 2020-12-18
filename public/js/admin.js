// sweetalert.jsを使用

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

//ページネーションボタン
function setUpPaginationBtns() {
    let pagination_btns = document.getElementsByName("pagination_btn");

    console.log(pagination_btns);
    for (var i = 0; i < pagination_btns.length; i++) {
        pagination_btns[i].addEventListener("click", function (e) {
            e.preventDefault();

            $('#posts_tbody').empty();
            $('#pagination_btns').empty();

            let link = $(this).attr('href');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: '/admin/search/',
                data: {
                    'page': link.slice(link.indexOf('?page=') + 6),
                    'title': $('#keyword_title').val(),
                    'body': $('#keyword_body').val(),
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                },
                dataType: 'json',
            }).done(function (data) {
                let html;
                let pagination_btns = '';

                console.log(data);

                $.each(data.data, function (index, value) {
                    let id = value.id;
                    let title = value.title;
                    let body = value.body;
                    let img = value.img;
                    let has_comments = value.has_comments;
                    let created_at = new Date(value.created_at).toLocaleString();
                    let _token = value._token;
                    let keywords = value.keywords;

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
                    `;

                    // 一応キーワードを持たせておく。
                    for (let name in keywords) {
                        html += `<input type="hidden" id="keyword_${name}" value="${keywords[name]}">`;
                    }
                    // 新しく生成するページの現在ページを更新するのを忘れずに。。
                });

                console.log(data);
                pagination_btns += '<nav><ul class="pagination">';
                for (let index in data.links) {
                    if (Number(index) === 0 && data.current_page === 1) {
                        continue;
                    }

                    if (Number(index) === data.links.length - 1 && data.current_page === data.last_page) {
                        continue
                    }

                    if (data.links[index].label === data.current_page) {
                        pagination_btns += `<li class="page-item active" aria-current="page"><span class="page-link">${data.links[index].label}</span></li>`;
                    } else {
                        pagination_btns += `<li class="page-item"><a name="pagination_btn" class="page-link" href="${data.links[index].url}">${data.links[index].label}</a></li>`;
                    }
                }
                pagination_btns += '</ul></nav>';

                // 取得してきたレコードを表示
                $('#posts_tbody').append(html);
                // 取得してきたレコードに応じてページネーションも表示
                $('#pagination_btns').append(pagination_btns);

                // htmlに表示している現在ページも更新しておく。
                $('#current_page').attr('value', data.current_page);

                setUpSingleDelete();
                setUpMultDeleteBtn();
                setUpPaginationBtns();

                if (data.length === 0) {
                    $('#posts_table').after('<p class="text-center mt-5 search-null">検索に一致する投稿は存在しません。</p>');
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log("ajax通信に失敗しました");
                console.log("jqXHR          : " + jqXHR.status); // HTTPステータスが取得
                console.log("textStatus     : " + textStatus);    // タイムアウト、パースエラー
                console.log("errorThrown    : " + errorThrown.message); // 例外情報
            });
        }, false);
    }
}
setUpPaginationBtns();

// 単一削除ボタン
function setUpSingleDelete() {
    let admin_delete_forms = document.getElementsByName('admin_delete_form');
    let admin_delete_btns = document.getElementsByName("admin_delete_btn");

    for (var i = 0; i < admin_delete_btns.length; i++) {
        admin_delete_btns[i].addEventListener("click", function (e) {
            e.preventDefault();

            // デフォルトは投稿削除
            let delete_content = 'post';
            let url = '/admin_delete';
            let data = {
                'current_page': document.getElementById('current_page').value,
                'title': $('#keyword_title').val(),
                'body': $('#keyword_body').val(),
                // これが多分取れてない。
                'post_id': $(this).prev('input').val(),
                '_token': $('meta[name="csrf-token"]').attr('content'),
            };

            // コメント削除の場合
            if (location.pathname.indexOf('admin_comment') > -1) {
                delete_content = 'comment';
                url = '/admin_comment_delete';
                data = {
                    'comment_id': $(this).prev('input').val(),
                    'post_id': location.pathname.replace('/admin_comment/', ''),
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                };
            }

            var options = {
                text: '本当に削除しますか？',
                buttons: {
                    ok: '削除する',
                    cancel: 'キャンセル',
                }
            };
            swal(options).then(function (value) {
                if (value) {
                    // これpostsいらない。
                    $('#posts_tbody').empty();

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: url,
                        data: data,
                        dataType: 'json',
                    }).done(function (data) {
                        let html;

                        // コメントページネーションも考えてね・
                        $.each(data.data, function (index, value) {
                            if (delete_content === 'post') {
                                console.log(value);

                                let id = value.id;
                                let title = value.title;
                                let body = value.body;
                                let img = value.img;
                                let has_comments = value.has_comments;
                                let created_at = new Date(value.created_at).toLocaleString();
                                let _token = value._token;
                                let keywords = value.keywords;

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
                                `;

                                for (let name in keywords) {
                                    html += `<input type="hidden" id="keyword_${name}" value="${keywords[name]}">`;
                                }
                            } else if (delete_content === 'comment') {
                                let id = value.id;
                                let body = value.body;
                                let created_at = new Date(value.created_at).toLocaleString();
                                let _token = value._token;

                                html += `
                                <tr>
                                    <th scope="row"><input type="checkbox" name="delete_checkbox" value="${id}"></th>
                                    <td>${id}</td>
                                    <td>${body}</td>
                                    <td>${created_at}</td>
                                    <td>
                                        <form name="admin_delete_form" style="display: inline-block;" method="post" action="/admin_delete">
                                            <input type="hidden" name="_token" value="${_token}">
                                            <input type="hidden" name="post_id" value="${id}">
                                            <button name="admin_delete_btn" class="btn btn-danger">削除</button>
                                        </form>
                                    </td>
                                </tr>
                                `;
                            }
                        });

                        $('#posts_tbody').append(html);

                        setUpSingleDelete();
                        setUpMultDeleteBtn();

                        if (data.data.length === 0) {
                            $('#posts_table').after('<p class="text-center mt-5 search-null">検索に一致する投稿は存在しません。</p>');
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        console.log("ajax通信に失敗しました");
                        console.log("jqXHR          : " + jqXHR.status); // HTTPステータスが取得
                        console.log("textStatus     : " + textStatus);    // タイムアウト、パースエラー
                        console.log("errorThrown    : " + errorThrown.message); // 例外情報
                    });
                }
            });

        }, false);
    }
}
setUpSingleDelete();

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
            // デフォルトは投稿削除
            let delete_content = 'post';
            let url = '/admin_mult_delete';
            let data = {
                'current_page': document.getElementById('current_page').value,
                'title': $('#keyword_title').val(),
                'body': $('#keyword_body').val(),
                'post_ids': delete_ids,
                '_token': $('meta[name="csrf-token"]').attr('content'),
            };

            // コメント削除の場合
            if (location.pathname.indexOf('admin_comment') > -1) {
                delete_content = 'comment';
                url = '/admin_mult_comment_delete';
                data = {
                    'post_id': location.pathname.replace('/admin_comment/', ''),
                    'comment_ids': delete_ids,
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                };
            }

            var options = {
                text: '本当に削除しますか？',
                buttons: {
                    ok: '削除する',
                    cancel: 'キャンセル',
                }
            };
            swal(options).then(function (value) {
                if (value) {
                    $('#posts_tbody').empty();

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: url,
                        data: data,
                        dataType: 'json',
                    }).done(function (data) {
                        let html;

                        $.each(data.data, function (index, value) {
                            if (delete_content === 'post') {
                                let id = value.id;
                                let title = value.title;
                                let body = value.body;
                                let img = value.img;
                                let has_comments = value.has_comments;
                                let created_at = new Date(value.created_at).toLocaleString();
                                let _token = value._token;
                                let keywords = value.keywords;

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
                                `;

                                for (let name in keywords) {
                                    html += `<input type="hidden" id="keyword_${name}" value="${keywords[name]}">`;
                                }
                            } else if (delete_content === 'comment') {
                                let id = value.id;
                                let body = value.body;
                                let created_at = new Date(value.created_at).toLocaleString();
                                let _token = value._token;

                                html += `
                                <tr>
                                    <th scope="row"><input type="checkbox" name="delete_checkbox" value="${id}"></th>
                                    <td>${id}</td>
                                    <td>${body}</td>
                                    <td>${created_at}</td>
                                    <td>
                                        <form name="admin_delete_form" style="display: inline-block;" method="post" action="/admin_delete">
                                            <input type="hidden" name="_token" value="${_token}">
                                            <input type="hidden" name="post_id" value="${id}">
                                            <button name="admin_delete_btn" class="btn btn-danger">削除</button>
                                        </form>
                                    </td>
                                </tr>
                                `;
                            }
                        });

                        $('#posts_tbody').append(html);

                        setUpSingleDelete();
                        setUpMultDeleteBtn();

                        if (data.data.length === 0) {
                            $('#posts_table').after('<p class="text-center mt-5 search-null">検索に一致する投稿は存在しません。</p>');
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        console.log("ajax通信に失敗しました");
                        console.log("jqXHR          : " + jqXHR.status); // HTTPステータスが取得
                        console.log("textStatus     : " + textStatus);    // タイムアウト、パースエラー
                        console.log("errorThrown    : " + errorThrown.message); // 例外情報
                    });
                }
            });
        }
    });
}
setUpMultDeleteBtn();

//検索
$('#admin_search_btn').on('click', function () {
    $('#posts_tbody').empty();
    $('#pagination_btns').empty();

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
        let html;
        let pagination_btns = '';

        // dataにはページネーションオブジェクトが入るので、さらにそこに存在するdataプロパティをまわす
        $.each(data.data, function (index, value) {
            let id = value.id;
            let title = value.title;
            let body = value.body;
            let img = value.img;
            let has_comments = value.has_comments;
            let created_at = new Date(value.created_at).toLocaleString();
            let _token = value._token;
            let keywords = value.keywords;

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
            `;

            // 一応キーワードを持たせておく。
            for (let name in keywords) {
                html += `<input type="hidden" id="keyword_${name}" value="${keywords[name]}">`;
            }
        });

        // コントローラーにおそら現在ページを送信しなければいけない。
        // このボタンを押すと、ajaxでページ番号が送信されて、コントローラーで表示するコンテンツを用意してまた、ビューに流す。
        console.log(data);
        pagination_btns += '<nav><ul class="pagination">';
        for (let index in data.links) {
            if (Number(index) === 0 && data.current_page === 1) {
                continue;
            }

            if (Number(index) === data.links.length - 1 && data.current_page === data.last_page) {
                continue
            }

            if (data.links[index].label === data.current_page) {
                pagination_btns += `<li class="page-item active" aria-current="page"><span class="page-link">${data.links[index].label}</span></li>`;
            } else {
                pagination_btns += `<li class="page-item"><a name="pagination_btn" class="page-link" href="${data.links[index].url}">${data.links[index].label}</a></li>`;
            }
        }
        pagination_btns += '</ul></nav>';

        // 取得してきたレコードを表示
        $('#posts_tbody').append(html);
        // 取得してきたレコードに応じてページネーションも表示
        $('#pagination_btns').append(pagination_btns);

        setUpSingleDelete();
        setUpMultDeleteBtn();
        setUpPaginationBtns();

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
