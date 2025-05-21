<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    require_once __DIR__ . '/vendor/autoload.php';

    require_once __DIR__ . '/includes/db.php';

    $mpdf = new \Mpdf\Mpdf();
    header('Content-Type: application/pdf');

    $stmt = $pdo->query("SELECT * FROM products");

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = 1;


    $html = '
        <html>
            <head>
                <style>
                    body { font-family: sans-serif; }
                </style>
            </head>
            <body>
                <h1>Product List</h1>
                <table border="1" cellspacing="0" cellpadding="5">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>';
    


    foreach ($products as $product) {

        $html .= '<tr>
                    <td>' . $count++ . '</td>
                    <td>' . htmlspecialchars($product['productname']) . '</td>
                    <td>' . htmlspecialchars($product['category']) . '</td>
                    <td>' . htmlspecialchars($product['price']) . '</td>
                    <td>' . htmlspecialchars($product['stock']) . '</td>
                  </tr>';
    }

    $html .= '
                    </tbody>
                </table>
            </body>
        </html>';

    $mpdf->WriteHTML($html);
    
    $mpdf->Output();
}
?>

<!DOCTYPE html>
<html lang = "en">
    <head>
        <meta charset="UTF-8">
        <title>Print Products</title>
        <link rel="/opo/bootstrap-5.3.3-dist/css/bootstrap.min.css" href="bootstrap/css/bootstrap.css">
    </head>
    <body>
        <form method="POST" action="">
            <button type="submit" class="btn btn-primary">
                Print Products
            </button>
        </form>
    </body>
</html>