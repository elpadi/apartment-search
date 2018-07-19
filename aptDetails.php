<?php
  
	require_once("conn/connApts.php");
  //Get the Variable passed in via the anchor link/URL
  $aptID = $_GET['aptID'];
  
  $query = "SELECT * FROM apartments JOIN buildings ON bldgID=IDbldg WHERE IDapt = '$aptID'";
  $result = mysqli_query($conn, $query);
  
  if (!$result) {
	  die(mysqli_error($conn));
  }
  //Test the result, expecting '1'
  //echo mysqli_affected_rows($conn);
  
  //Store the row retrieved from the database
  $row = mysqli_fetch_array($result);

	$colToLabel = [
		'bdrms' => 'No. of bedrooms',
		'baths' => 'No. of baths',
		'rent' => 'Monthly rent',
		'sqft' => 'Size',
		'floor' => 'Building floor',
		'isAvail' => 'Available',
		'aptDesc' => 'Description',
		'yearBuilt' => 'Building Date',
	];
  
?><!doctype html>
<html>
    
<head>
	<title>Apartment <?php echo $row['apt']; ?> Details, <?php echo $row['bldgName']; ?></title>
  <link href="css/apts.css" rel="stylesheet">
</head>
    
<body>

  <table border="1px" cellpadding="8px">
      
    <tr>
      <td colspan="3">
				<h1><?= sprintf('Apartment %s, <a href="bldgDetails.php?bldgID=%d">%s</a>', $row['apt'], $row['bldgID'], $row['bldgName']); ?></h1>
      </td>
    </tr>
      
    <tr>
      <td rowspan="2">
				<?php foreach (glob(__DIR__.'/images/propPics/'.str_replace(' ', '', $row['bldgName']).'Apt*.jpg') as $path)
					printf('<img src="images/propPics/%s">', basename($path)); ?>
      </td>
			<td>
				<?php foreach (['rent','bdrms','baths','sqft'] as $col) printf('<p>%s: %s</p>', $colToLabel[$col], ($col == 'rent' ? '$' : '').number_format($row[$col]).($col == 'sqft' ? ' Sqft' : '')); ?>
			</td>
			<td>
				<?php foreach (['floor','yearBuilt'] as $col) printf('<p>%s: %s</p>', $colToLabel[$col], number_format($row[$col])); ?>
				<?= $row['isAvail'] == 1 ? 'Currently Available' : 'Occupied'; ?>
			</td>
    </tr>
      
    <tr>
      <td colspan="2"><?php echo $row['aptDesc']; ?></td>
    </tr>
      
    <tr>
      <td><?php echo $row['address']; ?></td>
      <td><?= sprintf('<a href="tel:%s">(%s)%s-%s', $row['phone'], substr($row['phone'], 0, 3), substr($row['phone'], 3, 3), substr($row['phone'], 6)); ?></td>
      <td><?= sprintf('<a href="mailto:%s">%s</a>', $row['email'], $row['email']); ?></td>
    </tr>
      
  </table>


</body>
</html>
