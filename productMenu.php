<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AgroCulture</title>
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
            $('.add-to-cart-form').submit(function(event) {
                event.preventDefault();
                
                var form = $(this);
                var pid = form.find('input[name="pid"]').val();
                var quantity = form.find('input[name="quantity"]').val();

                $.ajax({
                    type: 'POST',
                    url: 'addToCart.php',
                    data: { pid: pid, quantity: quantity },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Product added to cart successfully!');
                            location.reload();  // Refresh the page to update availability
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
<body class>
    <?php require 'menu.php'; ?>

    <section id="main" class="wrapper style1 align-center">
        <div class="container">
            <h2>Welcome to digital market</h2>
            <?php
            if (isset($_GET['n']) && $_GET['n'] == 1):
            ?>
            <h3>Select Filter</h3>
            <form method="GET" action="productMenu.php?">
                <input type="hidden" name="n" value="1" />
                <center>
                    <div class="row">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-2">
                            <div class="select-wrapper" style="width: auto;">
                                <select name="type" id="type" required style="background-color:white;color: black;">
                                    <option value="all" style="color: black;">List All</option>
                                    <option value="fruit" style="color: black;">Fruit</option>
                                    <option value="vegetable" style="color: black;">Vegetable</option>
                                    <option value="grain" style="color: black;">Grains</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <input class="button special" type="submit" value="Go!" />
                        </div>
                        <div class="col-sm-4"></div>
                    </div>
                </center>
            </form>
            <?php endif; ?>

            <section id="two" class="wrapper style2 align-center">
                <div class="container">
                    <?php
                    $sql = "SELECT * FROM fproduct";
                    if (isset($_GET['type']) && $_GET['type'] != "all") {
                        $sql .= " WHERE pcat = '".mysqli_real_escape_string($conn, $_GET['type'])."'";
                    }
                    $result = mysqli_query($conn, $sql);

                    if (isset($_GET['success'])) {
                        echo "<p style='color:green;'>Product added to cart successfully!</p>";
                    } elseif (isset($_GET['error'])) {
                        if ($_GET['error'] == 'insufficient_stock') {
                            echo "<p style='color:red;'>Insufficient stock available.</p>";
                        } elseif ($_GET['error'] == 'product_not_found') {
                            echo "<p style='color:red;'>Product not found.</p>";
                        }
                    }
                    ?>
                    <div class="row">
                        <?php while ($row = $result->fetch_array()): ?>
                            <div class="col-md-4">
                                <section>
                                    <strong><h2 class="title" style="color:black;"><?php echo $row['product']; ?></h2></strong>
                                    <a href="review.php?pid=<?php echo $row['pid']; ?>">
                                        <img class="image fit" src="images/productImages/<?php echo $row['pimage']; ?>" height="220px;" />
                                    </a>
                                    <div style="text-align: left">
                                        <blockquote>
                                            <?php echo "Type : ".$row['pcat'];?><br>
                                            <?php echo "Price : ".$row['price'].' /-';?><br>
                                            <?php echo "Availability : ".$row['availability'];?>
                                        </blockquote>
                                        <form class="add-to-cart-form" method="POST">
                                            <input type="hidden" name="pid" value="<?php echo $row['pid']; ?>" />
                                            <label for="quantity">Quantity:</label>
                                            <input type="number" name="quantity" id="quantity" placeholder="Quantity" min="1" max="<?php echo $row['availability']; ?>" required />
                                            <br><br>
                                            <input class="button special" type="submit" value="Add to Cart" />
                                        </form>
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