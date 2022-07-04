<?php
    $config = include 'config.php';
    include 'pagination.php';

    $conn = new mysqli($config['host'], $config['name'], $config['pass'], $config['db']);
    if(!$conn) exit;

    $pagination = new Pagination([
        'perPage' => 5,
        'usePrevNextBtns' => true,
        'tooManyUseInput' => false
    ]);

    $query = "SELECT * FROM test_table WHERE id < 50 OR id > 60";

    $query = $pagination->formatQuery($query);

    $result = $conn->query($query);
    $data = $result->fetch_all(MYSQLI_BOTH);

    $pagination->setTotalItems($data[0]['total_rows'] ?? 0);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <table>
            <thead>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Random Number</th>
                <th>Random String</th>
            </thead>
            <tbody>
                <?php foreach($data as $row): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['random_num']; ?></td>
                        <td><?php echo $row['random_string']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
                        <?php echo $pagination->getPageButtonsHtml(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>