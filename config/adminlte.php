<?php

return [

    'title' => 'Nova 364',
    'title_prefix' => '',
    'title_postfix' => '',

    'use_ico_only' => true,
    'use_full_favicon' => false,

    'google_fonts' => [
        'allowed' => true,
    ],

    'logo' => false,
    'logo_img' => 'vendor/adminlte/dist/img/Nova_364_verde.jpg',
    'logo_img_class' => 'brand-image img',
    'logo_img_xl_class' => 'brand-image-xl',
    'logo_img_alt' => 'Nova 364',

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/Nova_364_verde.jpg',
            'alt' => 'Nova 364',
            'class' => 'img-fluid',
            'width' => 220,
            'height' => 90,
        ],
    ],

    'preloader' => [
        'enabled' => true,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/Nova_364_verde.jpg',
            'alt' => 'Nova 364',
            'effect' => 'animation__shake',
            'width' => 70,
            'height' => 70,
            'class' => 'img-fluid',
        ],
    ],

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => true,
    'usermenu_desc' => false,
    'usermenu_profile_url' => true,

    'layout_topnav' => true,
    'layout_boxed' => false,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    'classes_auth_card' => 'shadow border-0 rounded-lg',
    'classes_auth_header' => 'd-none',
    'classes_auth_body' => 'login-nova364-body',
    'classes_auth_footer' => 'text-center bg-white border-0',
    'classes_auth_icon' => 'fa-lg text-primary',
    'classes_auth_btn' => 'btn-flat btn bg-primary btn-block',

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => 'container-fluid',
    'classes_content' => 'container-fluid',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light border-bottom',
    'classes_topnav_nav' => 'navbar-expand-lg',
    'classes_topnav_container' => 'container-fluid',

    'sidebar_mini' => null,
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => 100,
    'sidebar_collapse_remember' => null,
    'sidebar_collapse_remember_no_transition' => null,
    'sidebar_scrollbar_theme' => 'os-theme-none',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    'menu' => [
        [
            'text' => 'Dashboard',
            'url' => 'home',
            'icon' => 'bi bi-speedometer2',
        ],

        [
            'text' => 'Empresas',
            'icon' => 'bi bi-building',
            'can'  => 'view empresas',
            'url'  => 'empresas',
        ],

        [
            'text' => 'Contratos',
            'icon' => 'bi bi-file-earmark-text',
            'can'  => 'view processos contratacao',
            'url'  => 'processos-contratacao',
        ],

        [
            'text' => 'Sistema',
            'icon' => 'bi bi-gear-fill',
            'submenu' => [
                [
                    'text' => 'Usuários',
                    'url'  => 'admin/users',
                    'can'  => 'view users',
                ],
                [
                    'text' => 'Perfil de Acesso',
                    'url'  => 'admin/roles',
                    'can'  => 'view roles',
                ],
                [
                    'text' => 'Permissões',
                    'url'  => 'admin/permissions',
                    'can'  => 'view permissions',
                ],
                [
                    'text' => 'Etapas Padrão',
                    'url'  => 'etapas-padrao',
                    'can'  => 'view etapas padrao',
                ],
                [
                    'text' => 'Setores',
                    'url'  => 'setores',
                    'can'  => 'view setores',
                ],
                [
                    'text' => 'Logs',
                    'url'  => 'admin/logs',
                    'can'  => 'view logs',
                ],
            ],
        ],
    ],

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js',
                ],
            ],
        ],

        'bootstrap-icons' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://kit.fontawesome.com/72f0f90082.js',
                ],
            ],
        ],

        'bootstrap' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css',
                ],
            ],
        ],

        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],

        'Chartjs' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/chart.js',
                ],
            ],
        ],

        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],

        'Pace' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],

        'CustomCSS' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/admin_custom.css',
                ],
            ],
        ],

        'CustomJS' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/admin_responsive.js',
                ],
            ],
        ],
    ],

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    'livewire' => false,
];
