<!DOCTYPE html>
<html>
<title>RidaRoni --Final Year Project</title>
<!--This will make our web page responsive -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<!--Our default styling sheet -->
<link rel="stylesheet" type="text/css" href="style.css">
<!--We use a font from font awesome to represent our logo -->
<link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css">
<!--We use Raleway font instead of default -->
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;900&display=swap" rel="stylesheet">


<body>
<!--HTML interface for user to search product -->
<header class="w3-container" style="background:#011f4b;color:#ffffff ">
  <a href="index.php" style="text-decoration: none;"><h1>RidaRoni<i class="fa fa-opencart" aria-hidden="true"></i></h1></a> 
</header>
<div class="w3-container" style="padding-top:200px; width:80%; margin:auto">

<form class="w3-container w3-card-4" action="index.php" method="get" >

 <p>
 <input class="w3-input" name="searchdata" type="text" placeholder="Search for product" title="Enter keyword such as 'Nokia'" required>
</p>
 <p>
 <input id="search" class="w3-input" type="submit" value="Search" style="background:#051e3e;color:#ffffff; margin-bottom: 20px; font-size: 1.75rem ">
</form>
<div class="w3-row-padding "  style="padding-top:210px; width:100%;margin:auto;">

<?php
//Fetch web page data using the URL
error_reporting(E_ALL & ~E_NOTICE);
if(isset($_GET['searchdata']))
{
  /*once the user enters searchdata display this
pass searchdata to the url as search*/
$search = $_GET['searchdata'];
$search = strtolower($search);
$search = str_replace(" ","+",$search);
  $web_page_data = file_get_contents("http://www.pricetree.com/search.aspx?q=".$search);

//we need to collect specific data from page we do not need to display entire page
  $item_list = explode('<div class="items-wrap">', $web_page_data);

  /*The code will split the data from the entire page based on word <div class="items-wrap">
  $item_list is an array so we
  print_r($item_list);*/
  $i=1;
  if(sizeof($item_list)<2){
    echo '<p><b>No results found, enter product name correctly Eg: Iphone</b></p>';
    $i=5;
  }

  /*we need a variable to check if there is no data
  and loop for only 4 items to print to screen*/
$count = 4;
  for($i;$i<5;$i++){

    /*echo $item_list[$i]; this is an array separated based on split string <div class="items-wrap">
    *We want title and other information
    *it is working on 4 items
    *for each of those items we want the item image url and item link
    *from the list of items we are going to split based on href=" and then " because we want url between them*/

    $url_link1 = explode('href="',$item_list[$i]);
    $url_link2 = explode('"', $url_link1[1]); 

    //this is the image link, same as above but split with data-original="

    $image_link1 = explode('data-original="',$item_list[$i]);
    $image_link2 = explode('"', $image_link1[1]);

    //we want the title of only available items

    $title1 = explode('title="', $item_list[$i]);
    $title2 = explode('"', $title1[1]);

    /*Show us only avaliable items
     *split between available stores*/

    $avaliavle1 = explode('avail-stores">', $item_list[$i]);
    $avaliable = explode('</div>', $avaliavle1[1]);
    if(strcmp($avaliable[0],"Not available") == 0) {
      $count = $count-1;
      continue;
      /*This means if the item we are looking for is not available move on to next item in loop*/
      }

    $item_title = $title2[0];
    if(strlen($item_title)<2){
      continue;
    }
    $item_link = $url_link2[0];
    $item_image_link = $image_link2[0];
    $item_id1 = explode("-", $item_link);
    $item_id = end($item_id1);

    //display image and product title
    echo '
    <br>
    <div class="w3-row">
    <div class="w3-col l2 w3-row-padding">
    <div class="w3-card-2" style="background-color:#011f4b;color:#ffffff;">
    <img src="'.$item_image_link.'" style="width:100%">
    <div class="w3-container">
    <h5>'.$item_title.'</h5>
    </div>
    </div>
    </div>
  ';


    /*We are making a request to pricetree to get list of prices
     *the prices list will be available based on $item_id*/

    $request = "http://www.pricetree.com/dev/api.ashx?pricetreeId=".$item_id."&apikey=7770AD31-382F-4D32-8C36-3743C0271699";
    $response = file_get_contents($request);
    $results = json_decode($response, TRUE);

    //print_r($results);
    //echo $results['count'];
    //We will create a table to display results
    //3 parts image and 9 parts table in a web page width

    echo '
    <div class="w3-col l8">
    <div class="w3-card-2">
      <table class="w3-table w3-striped w3-bordered w3-card-4">
      <thead>
      <tr class="w3-pale-blue">
        <th>Vendor</th>
        <th>Price(&#8377;)</th>
        <th>Purchase</th>
      </tr>
      </thead>
    ';
    foreach ($results['data'] as $itemdata) {
      $seller = $itemdata['Seller_Name'];
      $price = $itemdata['Best_Price'];
      $product_link = $itemdata['Uri'];
  echo '

      <tr>
        <td>'.$seller.'</td>
        <td>'.$price.'</td>
        <td><a href="'.$product_link.'" target="_black">Buy</a></td>
      </tr>

      ';
    }
    
    echo '
      </table>
      </div>
      </div>
      </div>
    ';
  }
  if($count == 0){
    echo '<p><b>No Products were found, Enter correct Product Name Eg: Laptop</b></p>';
  }
}
else {
  echo '<p>Search for the Best Price from online sites. <b>Search Product to Know Price from Online Shops</b></p>';
}
?>

</div>
</div>
</div>

<footer class="w3-container" style="background:#011f4b;color:#ffffff" >
<p>RDRN &copy;<script>document.write(new Date().getFullYear());</script></p>
</footer>
</body>
</html>
