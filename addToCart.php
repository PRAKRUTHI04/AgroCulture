<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pid = filter_var($_POST['pid'], FILTER_SANITIZE_NUMBER_INT);
    $quantity = filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT);
    $bid = $_SESSION['id'];

    // Check if the product exists and has enough stock
    $sql = "SELECT availability FROM fproduct WHERE pid = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($availability);
            $stmt->fetch();

            if ($availability >= $quantity) {
                // Add to cart
                $sql = "INSERT INTO mycart (bid, pid, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("iiii", $bid, $pid, $quantity, $quantity);
                    if ($stmt->execute()) {
                        // Update availability
                        $sql = "UPDATE fproduct SET availability = availability - ? WHERE pid = ?";
                        if ($stmt = $conn->prepare($sql)) {
                            $stmt->bind_param("ii", $quantity, $pid);
                            if ($stmt->execute()) {
                                echo json_encode(['status' => 'success']);
                            } else {
                                echo json_encode(['status' => 'error', 'message' => 'Failed to update product availability']);
                            }
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement for updating availability']);
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to add product to cart']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement for adding to cart']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Insufficient stock available']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>