// sweetalert.js functions.jsを使用
let options = {
    text: '本当に削除しますか？',
    buttons: {
        ok: '削除する',
        cancel: 'キャンセル',
    }
};

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
function setUpPaginationBtns(options) {
    $('.pagination_btn').click(function (e) {
        e.preventDefault();

        $('#posts_tbody').empty();
        $('#pagination_btns').empty();

        // デフォルトは投稿表示
        let link = $(this).attr('href');
        let show_content = 'post';
        let url = '/admin/search/';
        let data = {
            'page': link.slice(link.indexOf('?page=') + 6),
            'title': $('#keyword_title').val(),
            'body': $('#keyword_body').val(),
            '_token': $('meta[name="csrf-token"]').attr('content'),
        };

        // 投稿に紐づくコメント表示の場合
        if (location.pathname.indexOf('admin_comment') > -1) {
            show_content = 'comment';
            url = '/admin_comment/';
            data = {
                'page': link.slice(link.indexOf('?page=') + 6),
                'post_id': location.pathname.replace('/admin_comment/', ''),
                'ajax': 'true',
                '_token': $('meta[name="csrf-token"]').attr('content'),
            };
            // コメント検索の場合
            if (location.pathname.indexOf('admin_comment_list') > -1) {
                url = '/admin/comment_search/';
                data = {
                    'page': link.slice(link.indexOf('?page=') + 6),
                    'body': $('#keyword_body').val(),
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                };
            }
        }

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'GET',
            url: url,
            data: data,
            dataType: 'json',
        }).done(function (data) {
            let html;

            $.each(data.data, function (index, value) {
                if (show_content === 'post') {
                    html += getPostHtml(value, data);
                } else if (show_content === 'comment') {
                    html += getCommentHtml(value);
                }
            });

            $('#posts_tbody').append(html);
            $('#pagination_btns').append(getPaginationBtns(data));
            $('#current_page').attr('value', data.current_page);

            setUpSingleDeleteBtn(options);
            setUpMultDeleteBtn(options);
            setUpPaginationBtns(options);

            if (data.data.length === 0) {
                $('#posts_tbody').append('<p class="text-center mt-5 search-null">検索に一致する投稿は存在しません。</p>');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            outputAjaxError(jqXHR, textStatus, errorThrown);
        });
    });
}
setUpPaginationBtns(options);

// 単一削除ボタン
function setUpSingleDeleteBtn(options) {
    $('.admin_delete_btn').click(function (e) {
        e.preventDefault();

        // デフォルトは投稿削除
        let delete_content = 'post';
        let url = '/admin_delete';
        let data = {
            'page': document.getElementById('current_page').value,
            'title': $('#keyword_title').val(),
            'body': $('#keyword_body').val(),
            'post_id': $(this).prev('input').val(),
            '_token': $('meta[name="csrf-token"]').attr('content'),
        };

        // コメント削除の場合
        if (location.pathname.indexOf('admin_comment') > -1) {
            delete_content = 'comment';
            url = '/admin_comment_delete';
            data = {
                'page': document.getElementById('current_page').value,
                'comment_id': $(this).prev('input').val(),
                'post_id': location.pathname.replace('/admin_comment/', ''),
                '_token': $('meta[name="csrf-token"]').attr('content'),
            };

            if (location.pathname.indexOf('admin_comment_list') > -1) {
                data = {
                    'show_comment_list': true,
                    'body': $('#keyword_body').val(),
                    'page': document.getElementById('current_page').value,
                    'comment_id': $(this).prev('input').val(),
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                };
            }
        }

        swal(options).then(function (value) {
            if (value) {
                $('#posts_tbody').empty();

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'POST',
                    url: url,
                    data: data,
                    dataType: 'json',
                }).done(function (data) {
                    let html;

                    $.each(data.data, function (index, value) {
                        if (delete_content === 'post') {
                            html += getPostHtml(value, data);
                        } else if (delete_content === 'comment') {
                            html += getCommentHtml(value);
                        }
                    });

                    $('#posts_tbody').append(html);

                    setUpSingleDeleteBtn(options);
                    setUpMultDeleteBtn(options);

                    if (data.data.length === 0) {
                        $('#posts_tbody').append('<p class="text-center mt-5 search-null">検索に一致する投稿は存在しません。</p>');
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    outputAjaxError(jqXHR, textStatus, errorThrown);
                });
            }
        });
    });
}
setUpSingleDeleteBtn(options);

// まとめて削除ボタン
function setUpMultDeleteBtn(options) {
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
                'page': document.getElementById('current_page').value,
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
                    'page': document.getElementById('current_page').value,
                    'post_id': location.pathname.replace('/admin_comment/', ''),
                    'comment_ids': delete_ids,
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                };

                if (location.pathname.indexOf('admin_comment_list') > -1) {
                    data = {
                        'show_comment_list': true,
                        'body': $('#keyword_body').val(),
                        'page': document.getElementById('current_page').value,
                        'comment_ids': delete_ids,
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                    };
                }
            }

            swal(options).then(function (value) {
                if (value) {
                    $('#posts_tbody').empty();

                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        type: 'POST',
                        url: url,
                        data: data,
                        dataType: 'json',
                    }).done(function (data) {
                        let html;

                        $.each(data.data, function (index, value) {
                            if (delete_content === 'post') {
                                html += getPostHtml(value, data);
                            } else if (delete_content === 'comment') {
                                html += getCommentHtml(value);
                            }
                        });

                        $('#posts_tbody').append(html);

                        setUpSingleDeleteBtn(options);
                        setUpMultDeleteBtn(options);

                        if (data.data.length === 0) {
                            $('#posts_tbody').append('<p class="text-center mt-5 search-null">検索に一致する投稿は存在しません。</p>');
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        outputAjaxError(jqXHR, textStatus, errorThrown);
                    });
                }
            });
        }
    });
}
setUpMultDeleteBtn(options);

//投稿検索ボタン
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

        $.each(data.data, function (index, value) {
            html += getPostHtml(value, data);
        });

        $('#posts_tbody').append(html);
        $('#pagination_btns').append(getPaginationBtns(data));

        setUpSingleDeleteBtn(options);
        setUpMultDeleteBtn(options);
        setUpPaginationBtns(options);

        if (data.data.length === 0) {
            $('#posts_tbody').append('<p class="text-center mt-5 search-null">投稿が見つかりません</p>');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        outputAjaxError(jqXHR, textStatus, errorThrown);
    });
});

// 報告された投稿表示ボタン
$('#reported_posts_btn').on('click', function () {
    $('#posts_tbody').empty();
    $('#pagination_btns').empty();

    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'GET',
        url: '/admin/reported/',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'table_name': 'posts',
        },
        dataType: 'json',
    }).done(function (data) {
        let html;

        $.each(data.data, function (index, value) {
            html += getPostHtml(value, data);
        });

        $('#posts_tbody').append(html);
        $('#pagination_btns').append(getPaginationBtns(data));

        setUpSingleDeleteBtn(options);
        setUpMultDeleteBtn(options);
        setUpPaginationBtns(options);

        if (data.data.length === 0) {
            $('#posts_tbody').append('<p class="text-center mt-5 search-null">投稿が見つかりません</p>');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        outputAjaxError(jqXHR, textStatus, errorThrown);
    });
})

//コメント検索
$('#comment_search_btn').on('click', function () {
    $('#posts_tbody').empty();
    $('#pagination_btns').empty();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: '/admin/comment_search/',
        data: {
            'body': $('#keyword_body').val(),
            '_token': $('meta[name="csrf-token"]').attr('content'),
        },
        dataType: 'json',
    }).done(function (data) {
        let html;

        $.each(data.data, function (index, value) {
            html += getCommentHtml(value);
        });

        $('#posts_tbody').append(html);
        $('#pagination_btns').append(getPaginationBtns(data));

        setUpSingleDeleteBtn(options);
        setUpMultDeleteBtn(options);
        setUpPaginationBtns(options);

        if (data.data.length === 0) {
            $('#posts_tbody').append('<p class="text-center mt-5 search-null">コメントが見つかりません</p>');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        outputAjaxError(jqXHR, textStatus, errorThrown);
    });
});

// 報告されたコメント表示ボタン
$('#reported_comments_btn').on('click', function () {
    $('#posts_tbody').empty();
    $('#pagination_btns').empty();

    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'GET',
        url: '/admin/reported/',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'table_name': 'comments',
        },
        dataType: 'json',
    }).done(function (data) {
        let html;

        $.each(data.data, function (index, value) {
            html += getCommentHtml(value);
        });

        $('#posts_tbody').append(html);
        $('#pagination_btns').append(getPaginationBtns(data));

        setUpSingleDeleteBtn(options);
        setUpMultDeleteBtn(options);
        setUpPaginationBtns(options);

        if (data.data.length === 0) {
            $('#posts_tbody').append('<p class="text-center mt-5 search-null">投稿が見つかりません</p>');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        outputAjaxError(jqXHR, textStatus, errorThrown);
    });
})
