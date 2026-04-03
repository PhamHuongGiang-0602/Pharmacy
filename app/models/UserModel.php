<?php
// app/models/UserModel.php
class UserModel extends BaseModel {
    public function checkEmailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ? true : false;
    }

    public function createUser($data) {
        $sql = "INSERT INTO users (fullname, email, password, phone, gender, birthday, role_id) 
                VALUES (:fullname, :email, :password, :phone, :gender, :birthday, 2)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
}