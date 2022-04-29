<?php

/**
 * 	KalipsoNext - Localization File
 * 	Turkish(tr)
 **/

return [
    'lang' => [
        'code' => 'tr',
    ],
    'err' => 'Hata',
    'error' => [
        'page_not_found' => 'Sayfa bulunamadı!',
        'method_not_allowed' => 'Methoda izin verilmez!',
        'controller_not_defined' => 'Kontrolcü tanımlanmamış!',
        'unauthorized' => 'Yetkiniz yok.',
        'view_definition_not_found' => 'Kontrolcü, görüntüleme parametresi göndermedi!',
        'csrf_token_mismatch' => 'CSRF anahtarı uyuşmuyor.',
        'csrf_token_incorrect' => 'CSRF anahtarı geçersiz.',
        'username_is_already_used' => 'Kullanıcı adı zaten kullanımda.',
        'notification_hook_file_not_found' => 'Bildirim kanca dosyası bulunamadı!',
        'a_problem_occurred' => 'Bir sorun oluştu!',
        'endpoint_file_is_not_found' => 'Yetki kontrol noktası dosyası bulunamadı!'
    ],
    'notification' => [
        'registration_email_title' => 'Hesabınız Oluşturuldu!',
        'registration_email_body' => 'Selam [USER], <br>Hesabınız oluşturuldu. Aşağıdaki bağlantı ile eposta adresinizi doğrulayabilirsiniz. <br>[VERIFY_LINK]',
        'recovery_request_email_title' => 'Hesap Kurtarma',
        'recovery_request_email_body' => 'Selam [USER], <br>Hesap kurtarma talebinizi aldık. Aşağıdaki bağlantı ile yeni şifrenizi ayarlayabilirsiniz. <br>[RECOVERY_LINK]',
        'account_recovered_email_title' => 'Hesabınız Kurtarıldı!',
        'account_recovered_email_body' => 'Selam [USER], <br>Hesabınız kurtarıldı. Bu işlemi siz yapmadıysanız lütfen bizimle iletişime geçin.',
        'email_change_email_title' => 'Eposta Adresiniz Güncellendi!',
        'email_change_email_body' => 'Selam [USER], <br>Eposta adresiniz güncellendi. Aşağıdaki bağlantı ile doğrulama yapabilirsiniz. <br>[VERIFY_LINK] <br>[CHANGES]',
    ],
    'base' => [
        'sandbox' => 'Geliştirici Araçları',
        'sandbox_message' => 'Geliştirme sürecinde size yardımcı olacak tüm araçlara bu ekrandan ulaşabilirsiniz.',
        'clear_storage' => 'Klasörleri Temizle',
        'clear_storage_message' => 'Depolama klasörü içindeki dosyaları silmenizi sağlar.',
        'session' => 'Oturum',
        'session_message' => 'Oturum içindeki verileri gösterir.',
        'php_info' => 'PHP Bilgileri',
        'php_info_message' => 'Sunucu PHP bilgilerini gösterir.',
        'db_init' => 'Veri Tabanını Hazırla',
        'db_init_message' => 'Şemaya göre veri tabanı tablolarını hazırlar.',
        'db_init_success' => 'Veri tabanı başarıyla hazırlandı.',
        'db_init_problem' => 'Veritabanı hazırlanırken bir sorun oluştu. -> [ERROR]',
        'db_seed' => 'Veri Tabanını Doldur',
        'db_seed_message' => 'Şema içeriğinde verileri tablolara ekler.',
        'column' => 'Sütun',
        'data' => 'Veri',
        'table' => 'Tablo',
        'type' => 'Tip',
        'auto_inc' => 'Otomatik Artan',
        'attribute' => 'Özellik',
        'default' => 'Varsayılan',
        'index' => 'İndis',
        'yes' => 'evet',
        'no' => 'yes',
        'charset' => 'Karakter Seti',
        'collate' => 'Karşılaştırma Seti',
        'engine' => 'Motor',
        'db_name' => 'Veri Tabanı İsmi',
        'db_charset' => 'Veri Tabanı Karakter Seti',
        'db_collate' => 'Veri Tabanı Karşılaştırma Seti',
        'db_engine' => 'Veri Tabanı Motoru',
        'db_init_alert' => '[DB_NAME] adında bir veritabanı yoksa, [COLLATION] karşılaştırma seti ayarıyla ekleyin.',
        'db_init_start' => 'Harika, Hazırla!',
        'db_seed_success' => 'Veri tabanı başarıyla içe aktarıldı.',
        'db_seed_problem' => 'Veritabanı içe aktarılırken bir sorun oluştu. -> [ERROR]',
        'db_seed_start' => 'Harika, İçe Aktar!',
        'clear_storage_success' => 'Depolama klasörü temizlendi.',
        'folder' => 'Klasör',
        'delete' => 'Sil',
        'folder_not_found' => 'Klasör bulunamadı!',
        'change_language' => 'Dili Değiştir',
        'seeding' => 'İçe aktarılıyor...',
        'go_to_home' => 'Ana Sayfaya Dön',
        'home' => 'Ana Sayfa',
        'welcome' => 'Hoş geldiniz!',
        'welcome_message' => 'KalipsoNext\'in başlangıç sayfasıdır.',
        'login' => 'Giriş Yap',
        'login_message' => 'Örnek giriş sayfasıdır.',
        'register' => 'Kayıt Ol',
        'register_message' => 'Örnek kayıt sayfasıdır.',
        'logout' => 'Çıkış Yap',
        'account' => 'Hesap',
        'account_message' => 'Örnek hesap sayfasıdır.',
        'email_or_username' => 'Eposta ya da Kullanıcı Adı',
        'password' => 'Şifre',
        'recovery_account' => 'Hesabımı Kurtar',
        'recovery_account_message' => 'Bu sayfadan eposta adresinizi girerek şifre sıfırlama bağlantısı alabilirsiniz.',
        'email' => 'Eposta Adresi',
        'username' => 'Kullanıcı Adı', 
        'name' => 'Ad',
        'surname' => 'Soyad',
        'form_cannot_empty' => 'Form boş olamaz!',
        'email_is_already_used' => 'Eposta adresi zaten kullanılıyor.',
        'username_is_already_used' => 'Kullanıcı adı zaten kullanılıyor.',
        'registration_problem' => 'Kayıt esnasında bir sorun oluştu.',
        'registration_successful' => 'Kayıt başarılı!',
        'verify_email' => 'Eposta Adresini Doğrula',
        'verify_email_not_found' => 'Eposta doğrulama bağlantısı geçersiz!',
        'verify_email_problem' => 'Eposta doğrulaması yapılırken bir sorun oluştu!',
        'verify_email_success' => 'Eposta doğrulama başarılı.',
        'your_account_has_been_blocked' => 'Hesabınız silinmiş, lütfen iletişime geçin.',
        'account_not_found' => 'Hesap bulunamadı!',
        'your_login_info_incorrect' => 'Giriş bilgileriniz hatalı!',
        'welcome_back' => 'Tekrar hoş geldiniz!',
        'login_problem' => 'Oturum başlatılırken bir sorun oluştu.',
        'profile' => 'Profil',
        'profile_message' => 'Profilinizi bu sayfadan düzenleyebilirsiniz.',
        'sessions' => 'Oturumlar',
        'sessions_message' => 'Aktif oturumları bu sayfadan görüntüleyebilirsiniz.',
        'device' => 'Cihaz',
        'ip' => 'IP',
        'last_action_point' => 'Son İşlem Noktası',
        'last_action_date' => 'Son İşlem Tarihi',
        'action' => 'İşlem',
        'terminate' => 'Sonlandır',
        'session_terminated' => 'Oturum sonlandırıldı.',
        'session_not_terminated' => 'Oturum sonlandırılamadı!',
        'signed_out' => 'Çıkış yapıldı.',
        'login_information_updated' => 'Your login information has been updated.',
        'birth_date' => 'Doğum Tarihi',
        'update' => 'Güncelle',
        'save_problem' => 'Kaydedilirken bir sorun oluştu.',
        'save_success' => 'Başarıyla kaydedildi.',
        'recovery_request_successful' => 'Hesap kurtarma bağlantısını gönderdik, eposta kutunuzu kontrol etmeyi unutmayın.',
        'recovery_request_problem' => 'Hesap kurtarma bağlantısını gönderirken bir sorun oluştu.',
        'new_password' => 'Yeni Şifre',
        'change_password' => 'Şifreyi Değiştir',
        'account_recovered' => 'Hesap kurtarıldı, yeni şifrenizle giriş yapabilirsiniz.',
        'account_not_recovered' => 'Hesap kurtarılırken bir sorun oluştu.',
        'account_not_verified' => 'Hesap doğrulaması yapılmamış.',
        'management' => 'Yönetim',
        'toggle_navigation' => 'Navigasyonu Aç',
        'dashboard' => 'Kontrol Paneli',
        'dashboard_message' => 'Kontrol paneli neler olup bittiğini özet olarak görmenin en kısa yoludur.',
        'users' => 'Kullanıcılar',
        'user_roles' => 'Kullanıcı Rolleri',
        'logs' => 'Kayıtlar',
        'settings' => 'Ayarlar',
        'view' => 'Görüntüle',
    ],
    'app' => [
        
    ]
];