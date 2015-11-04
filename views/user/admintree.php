<?= \yiidreamteam\jstree\JsTree::widget([
    'containerOptions' => [
        'class' => 'data-tree',
    ],
    'jsOptions' => [
        'core' => [
            'multiple' => true,
            'data' => $data,
            'themes' => [
                'dots' => true,
                'icons' => false,
            ]
        ],
    ]
]) ?>