<?php

return [
    // 違反報告を受信する管理者メールアドレス
    'emails' => 'clu363721@gmail.com',

    // 報告メールの送信者
    'from' => 'from@example.com',

    // 対象モデル（キーはテーブル名）
    'targets' => [
        'posts' => [
            'model' => 'App\Models\Post',
            'url' => 'post/{id}'
        ],
    ],
];
