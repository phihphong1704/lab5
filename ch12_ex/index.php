<?php
// Start session management with a persistent cookie that lasts for 3 years
$lifetime = 60 * 60 * 24 * 365 * 3; // 3 years in seconds
session_set_cookie_params($lifetime, '/');
session_start();

// Create a cart array if needed
if (empty($_SESSION['cart12'])) {
    $_SESSION['cart12'] = array();
}

// Create a table of products
$products = array();
$products['MMS-1754'] = array('name' => 'Flute', 'cost' => '149.50');
$products['MMS-6289'] = array('name' => 'Trumpet', 'cost' => '199.50');
$products['MMS-3408'] = array('name' => 'Clarinet', 'cost' => '299.50');

// Include cart functions
require_once('cart.php');

// Get the action to perform
$action = filter_input(INPUT_POST, 'action');
if ($action === NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action === NULL) {
        $action = 'show_add_item';
    }
}

// Add or update cart as needed
switch($action) {
    case 'add':
        $product_key = filter_input(INPUT_POST, 'productkey');
        $item_qty = filter_input(INPUT_POST, 'itemqty');
        add_item($product_key, $item_qty);
        include('cart_view.php');
        break;
    case 'update':
        $new_qty_list = filter_input(INPUT_POST, 'newqty', FILTER_DEFAULT, 
                                     FILTER_REQUIRE_ARRAY);
        foreach($new_qty_list as $key => $qty) {
            if ($_SESSION['cart12'][$key]['qty'] != $qty) {
                update_item($key, $qty);
            }
        }
        include('cart_view.php');
        break;
    case 'show_cart':
        include('cart_view.php');
        break;
    case 'show_add_item':
        include('add_item_view.php');
        break;
    case 'empty_cart':
        unset($_SESSION['cart12']);
        include('cart_view.php');
        break;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Guitar Shop</title>
    <link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
    <header>
        <h1>My Guitar Shop</h1>
    </header>
    <main>
        <h1>Your Cart</h1>
        <?php if (empty($_SESSION['cart12']) || count($_SESSION['cart12']) == 0) : ?>
            <p>There are no items in your cart.</p>
        <?php else: ?>
            <form action="." method="post">
                <input type="hidden" name="action" value="update">
                <table>
                    <!-- Thêm cột ID vào bảng -->
                    <tr id="cart_header">
                        <th class="left">ID</th>
                        <th class="left">Item</th>
                        <th class="right">Item Cost</th>
                        <th class="right">Quantity</th>
                        <th class="right">Item Total</th>
                    </tr>

                    <?php foreach ($_SESSION['cart12'] as $key => $item) :
                        $id    = $key;
                        $cost  = number_format($item['cost'], 2);
                        $total = number_format($item['total'], 2);
                    ?>
                        <tr>
                            <td class="left">
                                <?php echo $id; ?>
                            </td>
                            <td>
                                <?php echo $item['name']; ?>
                            </td>
                            <td class="right">
                                $<?php echo $cost; ?>
                            </td>
                            <td class="right">
                                <input type="text" class="cart_qty" name="newqty[<?php echo $key; ?>]"
                                    value="<?php echo $item['qty']; ?>">
                            </td>
                            <td class="right">
                                $<?php echo $total; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <tr id="cart_footer">
                        <td colspan="4"><b>Subtotal</b></td>
                        <td>$<?php echo get_subtotal(); ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="right">
                            <input type="submit" value="Update Cart">
                        </td>
                    </tr>
                </table>
                <p>Click "Update Cart" to update quantities in your cart. Enter a quantity of 0 to remove an item.</p>
            </form>
        <?php endif; ?>
        <p><a href=".?action=show_add_item">Add Item</a></p>
        <p><a href=".?action=empty_cart">Empty Cart</a></p>
    </main>
</body>
</html>
