<?php
include dirname(__FILE__) . "/app/app.php";
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebChangesTracker</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
<h1>WebChangesTracker</h1>
<table>
<tbody>
<?php
foreach($db->website() as $website) {
    echo '<tr>';
    echo '      <td><b>'.$website['label'].'</b></td>';
    echo '      <td><a href="./view.php?id='.$website['id'].'">Preview</a></td>';
    echo '</tr>';
}
?>
</tbody>
</table>
</body>
</html>