<?php

return [
    [
        'label' => 'Usuários',
        'description' => 'Permissões relacionadas à gestão de usuários do sistema.',
        'permissions' => [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'assign roles to users',
            'assign direct permissions to users',
        ],
    ],
    [
        'label' => 'Roles e Permissões',
        'description' => 'Gerenciar perfis de acesso e permissões do projeto.',
        'permissions' => [
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'assign permissions to roles',
        ],
    ],
    [
        'label' => 'Empresas',
        'description' => 'Permissões relacionadas ao cadastro de empresas.',
        'permissions' => [
            'view empresas',
            'create empresas',
            'edit empresas',
            'delete empresas',
        ],
    ],
    [
        'label' => 'Processos de Contratação',
        'description' => 'Permissões do fluxo principal de contratos e propostas.',
        'permissions' => [
            'view processos contratacao',
            'create processos contratacao',
            'edit processos contratacao',
            'delete processos contratacao',
            'manage etapas processos contratacao',
        ],
    ],
    [
        'label' => 'Etapas Padrão',
        'description' => 'Gerenciar o cadastro de etapas padrão usadas nos fluxos.',
        'permissions' => [
            'view etapas padrao',
            'create etapas padrao',
            'edit etapas padrao',
            'delete etapas padrao',
        ],
    ],
    [
        'label' => 'Setores',
        'description' => 'Gerenciar setores e permissões de usuários por setor.',
        'permissions' => [
            'view setores',
            'create setores',
            'edit setores',
            'delete setores',
        ],
    ],
    [
        'label' => 'Aditivos',
        'description' => 'Permissões relacionadas à gestão de aditivos do contrato.',
        'permissions' => [
            'view aditivos',
            'create aditivos',
            'edit aditivos',
            'delete aditivos',
        ],
    ],

    [
        'label' => 'Dashboard',
        'description' => 'Permissões do módulo executivo, operacional e personalizado de dashboards.',
        'permissions' => [
            'view dashboard',
            'manage dashboard',
            'create dashboard personalizado',
            'edit dashboard personalizado',
            'delete dashboard personalizado',
            'share dashboard personalizado',
        ],
    ],
    [
        'label' => 'Logs e Histórico',
        'description' => 'Acesso aos registros do sistema e histórico operacional.',
        'permissions' => [
            'view logs',
        ],
    ],
];
