<?php
include dirname(__FILE__) . "/app/app.php";
include dirname(__FILE__) . "/app/app.login.php";
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebChangesTracker</title>
    <link rel="stylesheet" href="./style.css?v=4">
</head>
<body>
<h1>WebChangesTracker</h1>
<table>
<tbody>

<?php
foreach($db->website() as $website) {
    echo '<tr>';
    echo '      <td><b>'.$website['label'].'</b></td>';
    echo '      <td><a href="'.$website['url'].'">Open</a></td>';
    echo '      <td><a href="./view.php?id='.$website['id'].'">Preview</a></td>';
    echo '      <td><a href="./editor/index.php?username='.urlencode(EMAIL_ADDRESS).'&amp;edit=website&amp;'.urlencode('where[id]').'='.$website['id'].'">Edit</a></td>';
    echo '</tr>';
}
?>
</tbody>
</table>

<hr />
<a href="./editor/index.php?username=<?php echo urlencode(EMAIL_ADDRESS); ?>">Editor</a> &#124;
<a href="./editor/index.php?username=<?php echo urlencode(EMAIL_ADDRESS); ?>&amp;edit=website">Add new website</a> &#124;
<a href="./?logout">Logout</a> &#124;
<?php if(isset($_GET['run'])) { ?>
    <a href="./">Close tracker</a>
<hr />
<iframe src="./cron.php"></iframe>
<?php } else { ?>
    <a href="./?run">Run tracker manualy</a>
<?php } ?>
</body>
</html>