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
