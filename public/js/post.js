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


// これ他のページでエラーが起きちゃうけど。
let category = document.getElementById('edit_category');
if (category.dataset.category) {
    for (let i = 0; i < category.options.length; i++) {
        if (category.options.item(i).value === category.dataset.category) {
            console.log(category.options.item(i));
            category.options.item(i).selected = true;
        }
    }
}
