<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modeller
    |--------------------------------------------------------------------------
    |
    | Permission ve Role modellerinin hangi sınıfları kullanacağını belirtir.
    | Özelleştirilmiş modeller kullanıyorsanız burayı değiştirebilirsiniz.
    */
    'models' => [
        'permission' => Spatie\Permission\Models\Permission::class,
        'role' => Spatie\Permission\Models\Role::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tablo İsimleri
    |--------------------------------------------------------------------------
    |
    | Paketin kullanacağı veritabanı tablo isimleri.
    | Eğer tablo isimlerini değiştirmek isterseniz buradan yapabilirsiniz.
    */
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',

        // Model-Permission ilişki tablosu
        'model_has_permissions' => 'model_has_permissions',

        // Model-Role ilişki tablosu
        'model_has_roles' => 'model_has_roles',

        // Role-Permission ilişki tablosu
        'role_has_permissions' => 'role_has_permissions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sütun İsimleri
    |--------------------------------------------------------------------------
    |
    | İlişki tablolarındaki sütun isimlerini özelleştirebilirsiniz.
    | Özellikle UUID gibi farklı primary key tipleri kullanıyorsanız bu ayarları değiştirin.
    */
    'column_names' => [
        // Role ilişkileri için pivot key
        'role_pivot_key' => null, // Varsayılan: 'role_id'

        // Permission ilişkileri için pivot key
        'permission_pivot_key' => null, // Varsayılan: 'permission_id'

        // Polimorfik ilişkilerde model ID sütunu
        'model_morph_key' => 'model_id',

        // Takım özelliği kullanılıyorsa takım foreign key
        'team_foreign_key' => 'team_id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Yetki Kontrol Metodu
    |--------------------------------------------------------------------------
    |
    | Laravel Gate'de otomatik olarak yetki kontrol metodu eklenip eklenmeyeceği.
    | Özel bir yetki kontrol mantığı kullanacaksanız false yapın.
    */
    'register_permission_check_method' => true,

    /*
    |--------------------------------------------------------------------------
    | Octane Desteği
    |--------------------------------------------------------------------------
    |
    | Octane kullanıyorsanız ve permission cache'inde sorun yaşıyorsanız bu ayarı true yapın.
    | Normalde gerek yoktur.
    */
    'register_octane_reset_listener' => false,

    /*
    |--------------------------------------------------------------------------
    | Event'ler
    |--------------------------------------------------------------------------
    |
    | Role veya permission atama/çıkarma işlemlerinde event tetiklenip tetiklenmeyeceği.
    | Bu event'leri dinlemek için true yapıp listener'lar oluşturabilirsiniz.
    */
    'events_enabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Takım Özelliği
    |--------------------------------------------------------------------------
    |
    | Kullanıcıları takımlara göre yetkilendirme özelliği.
    | Aktif etmeden önce migrations'ları çalıştırmamış olmalısınız.
    */
    'teams' => false,

    /*
    |--------------------------------------------------------------------------
    | Takım Çözümleyici
    |--------------------------------------------------------------------------
    |
    | Takım ID'sini çözmek için kullanılacak sınıf.
    | Özel bir takım yapınız varsa bu sınıfı değiştirebilirsiniz.
    */
    'team_resolver' => \Spatie\Permission\DefaultTeamResolver::class,

    /*
    |--------------------------------------------------------------------------
    | Passport Desteği
    |--------------------------------------------------------------------------
    |
    | API yetkilendirmesi için Passport Client Credentials Grant kullanılıp kullanılmayacağı.
    */
    'use_passport_client_credentials' => false,

    /*
    |--------------------------------------------------------------------------
    | Hata Mesajlarında Yetki Bilgisi
    |--------------------------------------------------------------------------
    |
    | Yetki hatası verdiğinde hangi yetkinin eksik olduğunu hata mesajında gösterip göstermeyeceği.
    | Güvenlik nedeniyle varsayılan false'tur.
    */
    'display_permission_in_exception' => false,

    /*
    |--------------------------------------------------------------------------
    | Hata Mesajlarında Rol Bilgisi
    |--------------------------------------------------------------------------
    |
    | Rol hatası verdiğinde hangi rolün eksik olduğunu hata mesajında gösterip göstermeyeceği.
    | Güvenlik nedeniyle varsayılan false'tur.
    */
    'display_role_in_exception' => false,

    /*
    |--------------------------------------------------------------------------
    | Joker Yetki Desteği
    |--------------------------------------------------------------------------
    |
    | Wildcard (*) kullanarak yetki kontrolü yapılıp yapılmayacağı.
    | Örneğin: posts.* gibi yetkileri kontrol etmek için true yapın.
    */
    'enable_wildcard_permission' => false,

    /*
    |--------------------------------------------------------------------------
    | Cache Ayarları
    |--------------------------------------------------------------------------
    |
    | Yetki ve rol bilgilerinin cache'lenme ayarları.
    | Performans için önerilen cache süresi 24 saattir.
    */
    'cache' => [
        // Cache süresi (24 saat varsayılan)
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        // Cache key'i
        'key' => 'spatie.permission.cache',

        // Hangi cache deposunun kullanılacağı
        'store' => 'default',
    ],
];
