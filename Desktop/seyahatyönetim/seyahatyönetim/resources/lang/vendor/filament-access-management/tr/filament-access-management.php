<?php

return [

    'field' => [
        'id' => 'ID',
        'user' => [
            'name' => 'Ad',
            'email' => 'E-posta',
            'roles' => 'Roller',
            'verified_at' => 'Doğrulandı',
            'created_at' => 'Katılma Tarihi',
            'password' => 'Şifre',
            'confirm_password' => 'Şifreyi Onayla',
        ],
        'menu' => [
            'root' => 'Kök',
            'icon' => 'Simge',
            'parent' => 'Üst Menü',
            'uri' => 'Bağlantı',
            'is_filament_panel' => 'Filament Paneli mi?',
        ],
        'guard_name' => 'Koruma Adı',
        'title' => 'Başlık',
        'name' => 'Ad',
        'permissions' => 'İzinler',
        'roles' => 'Roller',
        'http_path' => 'HTTP Yolu',
        'created_at' => 'Oluşturulma',
        'updated_at' => 'Güncellenme',
    ],

    'section' => [
        'group' => 'Yönetici',
        'menu' => 'Menü',
        'users' => 'Kullanıcılar',
        'user' => 'Kullanıcı',
        'permission' => 'İzin',
        'permissions' => 'İzinler',
        'role' => 'Rol',
        'roles' => 'Roller',
        'roles_and_permissions' => 'Roller ve İzinler',
        'navigation' => 'Gezinme',
    ],

    'filter' => [
        'verified' => 'E-posta Doğrulandı',
    ],

    'button' => [
        'impersonate' => 'Kullanıcıyı Taklit Et',
    ],

    'text' => [
        'impersonating' => 'Taklit Edilen Kullanıcı: ',
        'impersonating_end' => ' - Taklidi Bitir ',
    ],

    'errors' => [
        'default' => 'Hata',
        'deny' => 'Reddedildi',
    ],

    'resource' => [
        'label' => 'Kaynak',
        'labels' => 'Kaynaklar',
    ],

    'expense' => [
        'label' => 'Gider',
        'plural' => 'Giderler',
    ],

    'user' => [
        'label' => 'Kullanıcı',
        'plural' => 'Kullanıcılar',
    ],

    'menu' => [
        'label' => 'Menü',
        'plural' => 'Menüler',
    ],

    'role' => [
        'label' => 'Rol',
        'plural' => 'Roller',
    ],

    'permission' => [
        'label' => 'İzin',
        'plural' => 'İzinler',
    ],
];
