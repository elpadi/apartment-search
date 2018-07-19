<?php 

// 2 + 3.) Connect to mysql, and select the database
require_once("conn/connApts.php");

// 4.) write out the CRUD "order" (query) -- what you want to do
$query = "
	SELECT *
	FROM apartments
	JOIN buildings ON IDbldg=bldgID
	JOIN neighborhoods ON IDhood=hoodID
";

$wheres[] = ['rent','BETWEEN',"$_GET[minRent] AND $_GET[maxRent]"];

foreach (['bldgID','bdrms','baths'] as $n) if ($_GET[$n] != -1) $wheres[] = [$n, strpos($_GET[$n], '+') ? '>=' : '=', str_replace('+', '', $_GET[$n])];

// concat query for checkboxes -- "check" to see, one by one, if the checkboxes are actually checked
foreach (['doorman','pets','parking','gym'] as $am) if (isset($_GET[$am])) $wheres[] = ['is'.ucfirst($am),'=','1'];

$query .= 'WHERE '.implode(' AND ', array_map(function($w) { return implode(' ', $w); }, $wheres));

$query .= " ORDER BY sqft DESC";

  // Order by *columnName* *ASC/DESC* <-- Sort based on a column

// 5.) execute the order: read records from apartments table

$result = mysqli_query($conn, $query);  // the result will be an array of arrays (or, a multi-dimensional array)

if (!$result) die("'$query' ".mysqli_error($conn));
?><!doctype html>

<html lang="en-us">
    
<head>
    
    <meta charset="utf-8">
    
    <title>Member Join Processor</title>

		<style>
body {
	background-color: palegoldenrod;
}
table {
	width: 100%;
	text-align: center;
	background-color: aqua;
	color: darkblue;
}
table h1 {
	color: teal;
}
th {
	color: royalblue;
}
thead tr:nth-child(1) {
	background-color: aliceblue;
}
thead tr:nth-child(2) {
	background-color: beige;
}
tbody tr:nth-child(odd) {
	background-color: blanchedalmond;
}
tbody tr:nth-child(even) {
	background-color: floralwhite;
}
table a {
	color: chocolate;
	text-decoration: none;
}
td[data-val="0"] {
	opacity: 0.6;
}
</style>
    
</head>

<body>
    
    
    
    <table border="1" cellpadding="5">
    
			<thead>
				<tr>
					<td colspan="14" align="center">
						<h1 align="center">Lofty Heights Apartments</h1>
					</td>
				</tr>
        <tr>
            <th>ID</th>
            <th>Apt</th>
            <th>Building</th>
            <th>Bedrooms</th>
            <th>Baths</th>
            <th>Rent</th>
            <th>Floor</th>
            <th>Sqft</th>
            <th>Status</th>
            <th>Neighborhood</th>
            <th>Doorman</th>
            <th>Pets</th>
            <th>Gym</th>
            <th>Parking</th>

        </tr>
			</thead>
			<tbody>
        
        <?php
        if (mysqli_num_rows($result) == 0): ?>
				<tr>
					<td colspan="14" align="center">
						<h2 align="center">No Results Found!</h2>
					</td>
				</tr>
				<?php endif; while($row = mysqli_fetch_array($result)) { ?>
          
          <tr>
              <td><?php echo $row['IDapt']; ?></td>
              <td>
							<?php 
								echo '<a href="aptDetails.php?aptID=' 
										. $row['IDapt'] . '">' 
										. $row['apt'] . '</a>';
							?>
              </td>
              <td>
							<?php 
								echo '<a href="bldgDetails.php?bldgID=' 
										. $row['bldgID'] . '">' 
										. $row['bldgName'] . '</a>';
							?>
              </td>
              
              <td><?php
                              
                  // ternary as alternative to if-else
                  echo $row['bdrms'] == 0 ? 'Studio' : $row['bdrms'];
                           
                  // if-else version of the ternary above
//                  if($row['bdrms'] == 0) {
//                     echo 'Studio'; 
//                  } else {
//                      echo $row['bdrms'];
//                  }
                                                  
              ?>
              
              </td>
              <td><?php echo $row['baths']; ?></td>
              <td>$<?php echo number_format($row['rent']); ?></td>
              <td><?php echo $row['floor']; ?></td>
              <td><?php echo number_format($row['sqft']); ?></td>
              <td>
                <?php 
                    if($row['isAvail'] == 0) {
                      echo "Occupied";
                    } else { // value is 1
                      echo "Available";
                    }                
                ?>
              
              </td>
              <td><?php echo $row['hoodName']; ?></td>
							<?php foreach (['isDoorman','isPets','isGym','isParking'] as $k)
								printf('<td data-val="%d">%s</td>', $row[$k], $row[$k] == 0 ? 'No' : 'Yes'); ?>
          </tr>
        
        <?php } ?>
			</tbody>
			<tfoot>
    
    </table>
    
</body>
   
</html>
