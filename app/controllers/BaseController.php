<?php

class BaseController {
    /**
     * Load view với dữ liệu
     */
    protected function loadView($viewName, $data = []) {
        // Extract data để dùng như biến trong view
        extract($data);
        
        // Load view file
        $viewPath = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View không tồn tại: " . $viewPath);
        }
    }

    /**
     * Redirect to a URL
     */
    protected function redirect($url) {
        header("Location: " . $url);
        exit();
    }

    /**
     * Trả về JSON cho API
     */
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
