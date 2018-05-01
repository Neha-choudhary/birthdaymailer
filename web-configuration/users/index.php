<!-- By D.J. Murray (apie), 2016-10-27 -->
<?php
function switchorder($order){
  if ($order=='ASC') return 'DESC';
  if ($order=='DESC') return 'ASC';
  return '';
}
//including the database connection file
include_once("../conf/config.php");

$sort = 'name';
if (isset($_GET['sort'])) $sort = mysqli_real_escape_string($mysqli, $_GET['sort']);
$order = 'ASC';
if (isset($_GET['order'])) $order = mysqli_real_escape_string($mysqli, $_GET['order']);

$config_id1 = 1;
if (isset($_GET['config_id'])) $config_id1 = mysqli_real_escape_string($mysqli, $_GET['config_id']);

$configs_result = mysqli_query($mysqli, "SELECT config_id,config_name FROM config ORDER BY config_name ASC");
$result = mysqli_query($mysqli, "SELECT user_id,name,email,birthday,DATE_FORMAT( birthday, '%m-%d' ) as birthdaymonth, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) as age
 FROM users WHERE config_id=$config_id1 ORDER BY $sort $order ");
mysqli_close($mysqli);
$order = switchorder($order);
?>

<html>
<head>
<title>Birthday mailer - user configuration</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>

<body>
    <h1>Birthday mailer - user configuration</h1>
	  <br/>
    <form action=".">
      <select name="config_id" onchange="this.form.submit()">
        <?php
        while($configres = mysqli_fetch_array($configs_result)) {
          echo "<option value=\"".$configres['config_id']."\"";
          echo ($configres['config_id']==$config_id1 ? " selected" : "");
          echo ">".$configres['config_name'];
          echo "</option>";
        }
        ?>
      </select>
      <noscript> <input type="submit" name="submit" value="Select config"> </noscript>
    </form>
    <a href="add.php?config_id=<?php echo $config_id1; ?>">Add new user</a><br/><br/>

    <table width='80%' border=0>
        <tr bgcolor='#CCCCCC'>
          <td><a href="?config_id=<?php echo $config_id1; ?>&sort=name&order=<?php echo $order; ?>">Name</a></td>
          <td><a href="?config_id=<?php echo $config_id1; ?>&sort=email&order=<?php echo $order; ?>">Email</a></td>
          <td><a href="?config_id=<?php echo $config_id1; ?>&sort=birthday&order=<?php echo $order; ?>">Birthday</a></td>
          <td><a href="?config_id=<?php echo $config_id1; ?>&sort=birthdaymonth&order=<?php echo $order; ?>">Birthday (w/o year)</a></td>
          <td><a href="?config_id=<?php echo $config_id1; ?>&sort=age&order=<?php echo $order; ?>">Age</a></td>
          <td>Update</td>
        </tr>
        <?php
        while($res = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>".$res['name']."</td>";
            echo "<td>".$res['email']."</td>";
            echo "<td>".$res['birthday']."</td>";
            echo "<td>".$res['birthdaymonth']."</td>";
            echo "<td>".$res['age']."</td>";
            echo "<td><a href=\"edit.php?user_id=$res[user_id]\">Edit</a> | <form id=\"deleteForm\" action=\"delete.php\" method=\"post\"> <input type=\"hidden\" name=\"user_id\" value=$res[user_id]> <a href=\"#\" onClick=\"confirm('Are you sure you want to delete ".$res['name']."?')?document.getElementById('deleteForm').submit():null\">Delete</a></form></td>";
        }
        ?>
    </table>
</body>
</html>
