<?php
include dirname(__FILE__) . "/app/app.php";
include dirname(__FILE__) . "/app/app.login.php";

$search = isset($_POST['search']) ? $_POST['search'] : "";
$filter_active = isset($_POST['filter_active']) ? true : false;
$filter_inactive = isset($_POST['filter_inactive']) ? true : false;
$folder = isset($_POST['folder']) ? intval($_POST['folder']) : 0;

if(!isset($_POST['filter_send'])) {
    $filter_active = true;
    $filter_inactive = false;
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebChangesTracker</title>
    <link rel="stylesheet" href="./style.css?v=7">
</head>
<body>
<h1>WebChangesTracker</h1>

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

<hr />

<form action="./" method="post">
<p>
    <input type="text" name="search" placeholder="&#x1F50E;&#xFE0E;" value="<?php echo $search; ?>" />

    <select name="folder" id="folder">
        <option value="0"<?php echo ($folder==0 ? ' selected="selected"' : ''); ?>>(all folders)</option>
        <?php
            foreach($db->folder()->order("name ASC") as $item) {
                echo '<option value="'.$item['id'].'"'.($folder == $item['id'] ? ' selected="selected"' : '').'>'.$item['name'].'</option>';
            }
        ?>
    </select>

    <label for="filter_active"><input type="checkbox" name="filter_active" id="filter_active"<?php echo ($filter_active ? ' checked="checked"' : ''); ?> /> active</label>
    <label for="filter_inactive"><input type="checkbox" name="filter_inactive" id="filter_inactive"<?php echo ($filter_inactive ? ' checked="checked"' : ''); ?> /> inactive</label>

    <input type="submit" value="Filter" />

    <input type="hidden" name="filter_send" value="1" />
</p>
</form>

<form action="./records.php">
<table>

<thead>
<tr>
    <th>Label</th>
    <th>Folder</th>
    <th>Status</th>
    <th>Interval</th>
    <th>Last tracking</th>
    <th>URL</th>
    <th>Preview</th>
    <th><input type="checkbox" id="select_all" /> Records</th>
    <th>Edit</th>
</tr>
</thead>
<tbody>

<?php
$websites = $db->website();
if($search!="") $websites->where("LOWER(label) REGEXP ?", mb_strtolower($search));
if(!$filter_active) $websites->where("status", "inactive");
if(!$filter_inactive) $websites->where("status", "active");
if($folder>0) $websites->where("folder_id", $folder);

foreach($websites as $website) {

    echo '<tr>';
    echo '      <td><b>'.$website['label'].'</b></td>';
    echo '      <td>'.$website->folder["name"].'</td>';
    echo '      <td>'.$website['status'].'</td>';
    echo '      <td>'.$website['tracking_interval'].'</td>';
    echo '      <td>'.$website['tracking_last'].'</td>';
    echo '      <td><a href="'.$website['url'].'">URL</a></td>';
    echo '      <td><a href="./view.php?id='.$website['id'].'">Preview</a></td>';
    echo '      <td><input name="website[]" type="checkbox" value="'.$website['id'].'"/> <a href="./records.php?'.urlencode('website[]').'='.$website['id'].'">Records</a></td>';
    echo '      <td><a href="./editor/index.php?username='.urlencode(EMAIL_ADDRESS).'&amp;edit=website&amp;'.urlencode('where[id]').'='.$website['id'].'">Edit</a></td>';
    echo '</tr>';

}
?>
</tbody>
</table>
<p>
    <input type="submit" value="Show records" />
</p>
</form>


<script src="./jquery-3.6.0.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    $("#select_all").change(function (e) {
        $('input[name^="website"]').prop("checked", $(this).is(":checked"));
    });
});
</script>
</body>
</html>