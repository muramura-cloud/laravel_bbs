// リンクにクエリを付け足す。
function addUrlParam(path, key, value, save) {
    if (!path || !key || !value) return '';

    var addParam = key + '=' + value,
        paths = path.split('/'),
        fullFileName = paths.pop(),
        fileName = fullFileName.replace(/[\?#].+$/g, ''),
        dirName = path.replace(fullFileName, ''),
        hashMatches = fullFileName.match(/#([^#]+)$/),
        paramMatches = fullFileName.match(/\?([^\?]+)$/),
        hash = '',
        param = '',
        params = [],
        fullPath = '',
        hitParamIndex = -1;

    if (hashMatches && hashMatches[1]) {
        hash = hashMatches[1];
    }

    if (paramMatches && paramMatches[1]) {
        param = paramMatches[1].replace(/#[^#]+$/g, '').replace('&', '&');
    }

    fullPath = dirName + fileName;

    if (param === '') {
        param = addParam;
    } else if (save) {
        params = param.split('&');

        for (var i = 0, len = params.length; i < len; i++) {
            if (params[i].match(new RegExp('^' + key + '='))) {
                hitParamIndex = i;
                break;
            }
        }

        if (hitParamIndex > -1) {
            params[hitParamIndex] = addParam;
            param = params.join('&');
        } else {
            param += '&' + addParam;
        }
    } else {
        param += '&' + addParam;
    }

    fullPath += '?' + param;

    if (hash !== '') fullPath += '#' + hash;

    return fullPath;
};

//投稿表示のhtml paginate_dataにはコントラーラーのページネーターオブジェクトが入る。
function getPostHtml(value, paginate_data) {
    let html = '';

    let a_img = '画像なし';
    if (value.img) {
        a_img = `<a href="${value.img}"><img src="${value.img}" style = "width: 40px; height: 30px;"></a >`;
    }

    let a_comment = 'コメント無し';
    if (value.has_comments) {
        let url = `/admin_comment/${value.id}`;
        value.keywords.page = paginate_data.current_page;
        for (let name in value.keywords) {
            if (!value.keywords[name]) {
                continue;
            }
            url = addUrlParam(url, name, value.keywords[name], true);
        }
        a_comment = `<a href="${url}" class="btn">コメント一覧へ</a > `;
    }

    html += `
    <tr>
        <th scope="row"><input type="checkbox" name="delete_checkbox" value="${value.id}"></th>
        <td>${value.id}</td>
        <td>${value.title}</td>
        <td class="td-body" tabindex="0">${value.body}</td>
        <td>${a_img}</td>
        <td>${a_comment}</td>
        <td>${new Date(value.created_at).toLocaleString()}</td>
        <td>
            <form name="admin_delete_form" style="display: inline-block;" method="post" action="/admin_delete">
                <input type="hidden" name="_token" value="${value._token}">
                <input type="hidden" name="post_id" value="${value.id}">
                <button name="admin_delete_btn" class="btn btn-danger">削除</button>
            </form>
        </td>
    </tr>
    `;

    for (let name in value.keywords) {
        html += `<input type="hidden" id="keyword_${name}" value="${value.keywords[name]}">`;
    }

    return html;
}

// コメント表示のhtml
function getCommentHtml(value) {
    let html = '';

    html += `
    <tr>
        <th scope="row"><input type="checkbox" name="delete_checkbox" value="${value.id}"></th>
        <td>${value.id}</td>
        <td>${value.body}</td>
        <td>${new Date(value.created_at).toLocaleString()}</td>
        <td>
            <form name="admin_delete_form" style="display: inline-block;" method="post" action="/admin_delete">
                <input type="hidden" name="_token" value="${value._token}">
                <input type="hidden" name="post_id" value="${value.id}">
                <button name="admin_delete_btn" class="btn btn-danger">削除</button>
            </form>
        </td>
    </tr>
    `;

    return html;
}

//ページネーションボタン
function getPaginationBtns(paginate_data) {
    let pagination_btns = '';

    pagination_btns += '<nav><ul class="pagination">';
    for (let index in paginate_data.links) {
        if (Number(index) === 0 && paginate_data.current_page === 1) {
            continue;
        }

        if (Number(index) === paginate_data.links.length - 1 && paginate_data.current_page === paginate_data.last_page) {
            continue
        }

        if (paginate_data.links[index].label === paginate_data.current_page) {
            pagination_btns += `<li class="page-item active" aria-current="page"><span class="page-link">${paginate_data.links[index].label}</span></li>`;
        } else {
            pagination_btns += `<li class="page-item"><a name="pagination_btn" class="page-link" href="${paginate_data.links[index].url}">${paginate_data.links[index].label}</a></li>`;
        }
    }
    pagination_btns += '</ul></nav>';

    return pagination_btns;
}

// クエリをオブジェクトとして取得する
function getUrlQueries() {
    var queryStr = window.location.search.slice(1);  // 文頭?を除外
    queries = {};

    // クエリがない場合は空のオブジェクトを返す
    if (!queryStr) {
        return queries;
    }

    // クエリ文字列を & で分割して処理
    queryStr.split('&').forEach(function (queryStr) {
        // = で分割してkey,valueをオブジェクトに格納
        var queryArr = queryStr.split('=');
        queries[queryArr[0]] = queryArr[1];
    });

    return queries;
}
