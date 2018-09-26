<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Datagrid;

final class DefaultActions
{
    const UPDATE = [
        'icon'  => 'edit',
        'color' => 'secondary',
    ];

    const DELETE = [
        'icon'  => 'trash',
        'color' => 'danger',
        'attrs' => ['data-ask-delete' => null],
    ];

    const IMPORT = [
        'icon'  => 'download',
        'color' => 'secondary',
    ];

    const EXPORT = [
        'icon'  => 'upload',
        'color' => 'secondary',
    ];
}
