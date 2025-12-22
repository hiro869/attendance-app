<?php

return [

    'required' => ':attribute を入力してください',
    'email'    => 'メールアドレス形式で入力してください',
    'min'      => [
        'string' => ':attribute は :min 文字以上で入力してください',
    ],
    'max'      => [
        'string' => ':attribute は :max 文字以下で入力してください',
    ],
    'unique'   => ':attribute は既に使用されています',

    // カスタム属性名（英語 → 日本語）
    'attributes' => [
        'email'    => 'メールアドレス',
        'password' => 'パスワード',
        'name'     => 'お名前',
    ],

];
