<?php
include dirname(__FILE__) . "/app/app.php";
include dirname(__FILE__) . "/app/app.login.php";

$website = isset($_GET['website']) ? $_GET['website'] : array();
$limit = isset($_GET['limit']) ? $_GET['limit'] : 20;

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Records  &#124; WebChangesTracker</title>
    <link rel="stylesheet" href="./style.css?v=6">
</head>
<body>
<h1>Records</h1>
<form action="./records.php">
<?php
if(count($website) == 0) {
    echo '<p><em>No selected websites</em></p>';
} else {
    foreach($website as $id) {
        echo '<input type="hidden" name="website[]" value="'.$id.'" />';
    }
}
?>

<p><a href="./">&laquo; back</a></p>

<hr />

<p>
    <label for="limit">Limit:</label>
    <input type="number" name="limit" min="20" step="20" max="1000" value="<?php echo $limit; ?>" />
</p>

<p>
    <input type="submit" value="Show records" />
</p>

<?php
$records = $db->records()->where("website_id", $website)->order("occurrence_last DESC")->limit($limit);

foreach($records as $record) {
    $template = $App->getMessageTemplate($record['website_id']);
    echo '<div class="preview">';
    echo $App->createMessage($template, $record);
    echo '</div>';
}
?>

</form>
</body>
</html>