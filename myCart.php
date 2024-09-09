<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == 0) {
    $_SESSION['message'] = "You need to first login to access this page !!!";
    header("Location: Login/error.php");
    exit();
}

$bid = $_SESSION['id'];

// Handle AJAX request to remove item
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pid = filter_var($_POST['pid'], FILTER_SANITIZE_NUMBER_INT);

    // Delete item from mycart
    $sql = "DELETE FROM mycart WHERE bid = ? AND pid = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $bid, $pid);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed']);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AgroCulture: My Cart</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="login.css"/>
    <script src="js/jquery.min.js"></script>
    <script src="js/skel.min.js"></script>
    <script src="js/skel-layers.min.js"></script>
    <script src="js/init.js"></script>
    <noscript>
        <link rel="stylesheet" href="css/skel.css" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/style-xlarge.css" />
    </noscript>
    <script>
        $(document).ready(function() {
            $('.remove-btn').click(function(event) {
                event.preventDefault();
                
                var button = $(this);
                var pid = button.data('pid');
                
                $.ajax({
                    type: 'POST',
                    url: '', // The same file
                    data: { pid: pid },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            button.closest('.cart-item').remove(); // Remove the item from the DOM
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred.');
                    }
                });
            });
        });
    </script>
</head>
<body>

<?php
require 'menu.php';
?>

<section id="main" class="wrapper style1 align-center">
    <div class="container">
        <h2>My Cart</h2>

        <section id="two" class="wrapper style2 align-center">
            <div class="container">
                <div class="row">
                    <?php
                    $sql = "SELECT * FROM mycart WHERE bid = '$bid'";
                    $result = mysqli_query($conn, $sql);
                    while ($row = $result->fetch_array()):
                        $pid = $row['pid'];
                        $sql = "SELECT * FROM fproduct WHERE pid = '$pid'";
                        $result1 = mysqli_query($conn, $sql);
                        $row1 = $result1->fetch_array();
                        $picDestination = "images/productImages/" . $row1['pimage'];
                        ?>
                        <div class="col-md-4 cart-item">
                            <section>
                                <strong><h2 class="title" style="color:black;"><?php echo $row1['product']; ?></h2></strong>
                                <a href="review.php?pid=<?php echo $row1['pid']; ?>"><img class="image fit"
                                                                                         src="<?php echo $picDestination; ?>"
                                                                                         alt=""/></a>
                                <div style="text-align: left">
                                    <blockquote>
                                        <?php echo "Type: " . $row1['pcat']; ?><br>
                                        <?php echo "Price: " . $row1['price'] . ' /-'; ?><br>
                                        <?php echo "Quantity: " . $row['quantity']; ?><br>
                                    </blockquote>
                                    <button class="remove-btn" data-pid="<?php echo $pid; ?>">Remove</button>
                                </div>
                            </section>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    </div>
</section>

</body>
</html>