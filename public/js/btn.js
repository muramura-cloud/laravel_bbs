//検索項目切り替え
document.getElementsByName('search_name')[0].addEventListener('click', function () {
    if (document.getElementsByName('search_name')[0].checked) {
        document.getElementById('keyword_input').placeholder = '投稿者名キーワード';
        document.getElementById('do_name_search').value = 1;
    } else {
        document.getElementById('keyword_input').placeholder = '検索キーワード';
        document.getElementById('do_name_search').value = 0;
    }
}, false);

// コメント報告ボタンのgetパラメーターの調整
let comment_report_btns = document.getElementsByName('comment_report_btn');
for (let i = 0; i < comment_report_btns.length; i++) {
    let link = comment_report_btns[i].href;
    let comment_id = comment_report_btns[i].getAttribute('value');
    comment_report_btns[i].setAttribute('href', link.replace('target=posts', 'target=comments').concat(`&comment_id=${comment_id}`));
}
