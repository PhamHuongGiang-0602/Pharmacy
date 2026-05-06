<?php
// app/controllers/DoctorController.php

class DoctorController {
    public function dashboard() {
        // Bảo vệ route: Chỉ role_id == 3 (Bác sĩ) được vào
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
            header("Location: " . BASE_URL . "auth/login");
            exit();
        }

        $pageTitle = 'Dashboard Bác sĩ Tư vấn';
        require_once 'app/views/doctor/dashboard.php';
    }
}
?>
