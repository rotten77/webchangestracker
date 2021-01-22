<?php
include dirname(__FILE__) . "/app/app.php";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id>0) {
    $website = $db->website[$id];

    if(!$website) {
        die('Website not defined');
    }

} else {
    die('Website not defined');
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $website['label']; ?>  &#124; WebChangesTracker</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
<h1><?php echo $website['label']; ?></h1>
<p><a href="./">&laquo; back</a></p>
<table>
    <thead>
        <tr>
            <th colspan="2">XPath</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>content_wrapper</td><td><code><?php echo $website['content_wrapper']; ?></code></td>
        </tr>
            <td>content_id</td><td><code><?php echo $website['content_id']; ?></code></td>
        </tr>
        <tr>
            <td>content_item_1</td><td><code><?php echo $website['content_item_1']; ?></code></td>
        </tr>
        <tr>
            <td>content_item_2</td><td><code><?php echo $website['content_item_2']; ?></code></td>
        </tr>
        <tr>
            <td>content_item_3</td><td><code><?php echo $website['content_item_3']; ?></code></td>
        </tr>
        <tr>
            <td>content_item_4</td><td><code><?php echo $website['content_item_4']; ?></code></td>
        </tr>
        <tr>
            <td>content_item_5</td><td><code><?php echo $website['content_item_5']; ?></code></td>
        </tr>        
    </tbody>
</table>

<h2>Preview:</h2>
<?php
$template = $App->getMessageTemplate($website['id']);
$data = ($App->parseUrl($website['id']));

foreach($data as $item) {
    echo '<div class="preview">';
    echo $App->createMessage($template, $item);
    echo '</div>';
}
?>
</body>
</html>