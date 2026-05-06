<?php

require_once __DIR__ . '/../BaseController.php';

class AdminSettingsController extends BaseController {
    
    private string $settingsFile;

    public function __construct() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
            $this->redirect(BASE_URL . 'auth/login');
        }
        $this->settingsFile = __DIR__ . '/../../../config/settings.json';
    }

    private function loadSettings(): array {
        if (file_exists($this->settingsFile)) {
            return json_decode(file_get_contents($this->settingsFile), true) ?? [];
        }
        return [
            'site_name' => 'Nhà thuốc 1985',
            'email'     => 'admin@1985.com',
            'lang'      => 'vi',
            'notify'    => true,
            'twofa'     => false,
        ];
    }

    private function saveSettingsData(array $data): void {
        if (!is_dir(dirname($this->settingsFile))) {
            mkdir(dirname($this->settingsFile), 0777, true);
        }
        file_put_contents($this->settingsFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function index() {
        $settings = $this->loadSettings();
        $this->loadView('admin/settings/index', [
            'pageTitle' => 'Cài đặt hệ thống',
            'settings' => $settings
        ]);
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'site_name' => $_POST['site_name'] ?? 'Nhà thuốc 1985',
                'email'     => $_POST['email'] ?? '',
                'lang'      => $_POST['lang'] ?? 'vi',
                'notify'    => isset($_POST['notify']),
                'twofa'     => isset($_POST['twofa']),
            ];

            $this->saveSettingsData($data);
            
            $_SESSION['flash_success'] = "Đã lưu cấu hình thành công!";
            $this->redirect(BASE_URL . 'admin/settings');
        }
    }
}
