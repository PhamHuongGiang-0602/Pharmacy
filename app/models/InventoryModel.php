<?php

require_once __DIR__ . '/BaseModel.php';

class InventoryModel extends BaseModel {
    
    /**
     * Lấy tóm tắt tồn kho (từ View v_inventory_summary)
     */
    public function getInventorySummary() {
        $sql = "SELECT * FROM v_inventory_summary ORDER BY total_quantity ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy cảnh báo hạn sử dụng (từ View v_expiry_alerts)
     */
    public function getExpiryAlerts() {
        $sql = "SELECT * FROM v_expiry_alerts";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy danh sách lô hàng của một sản phẩm
     */
    public function getBatchesByProductId($productId) {
        $sql = "SELECT * FROM batches WHERE product_id = :pid ORDER BY expiry_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pid' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Thêm phiếu nhập kho mới và các lô hàng
     */
    public function addStockReceipt($data, $items) {
        try {
            $this->db->beginTransaction();
            
            // 1. Thêm phiếu nhập
            $sql = "INSERT INTO stock_receipts (supplier_id, user_id, receipt_date, invoice_number, total_amount, notes, status) 
                    VALUES (:supplier_id, :user_id, :r_date, :inv_num, :total, :notes, 'completed')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'supplier_id' => $data['supplier_id'],
                'user_id' => $data['user_id'],
                'r_date' => $data['receipt_date'],
                'inv_num' => $data['invoice_number'],
                'total' => $data['total_amount'],
                'notes' => $data['notes'] ?? ''
            ]);
            $receiptId = $this->db->lastInsertId();
            
            // 2. Thêm từng lô hàng
            foreach ($items as $item) {
                $sqlBatch = "INSERT INTO batches (product_id, receipt_id, batch_number, manufacture_date, expiry_date, 
                                               quantity_received, quantity_remaining, purchase_price, selling_price, storage_location) 
                             VALUES (:pid, :rid, :b_num, :m_date, :e_date, :qty, :qty, :p_price, :s_price, :loc)";
                $stmtBatch = $this->db->prepare($sqlBatch);
                $stmtBatch->execute([
                    'pid' => $item['product_id'],
                    'rid' => $receiptId,
                    'b_num' => $item['batch_number'],
                    'm_date' => $item['manufacture_date'],
                    'e_date' => $item['expiry_date'],
                    'qty' => $item['quantity'],
                    'p_price' => $item['purchase_price'],
                    's_price' => $item['selling_price'],
                    'loc' => $item['storage_location'] ?? ''
                ]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Lỗi nhập kho: " . $e->getMessage());
            return false;
        }
    }
}
