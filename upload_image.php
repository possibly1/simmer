<?php
include_once "sql_connect.php";
include_once "functions.php";

//Verification that the person is actually logged in before he can edit any pictures lol
if (!isset($_SESSION['id']))
{
    die ("<a href='login.php'>Login</a>");
}

//set the userid vairbale as $_SESSION['id']. easier for everyone.
$userid = $_SESSION['id'];

//If someone clicked the remove link under an image, remove it. immedaitely.
if (isset($_GET['remove']))
{
    $remove = $_GET['remove'];
    mysql_query("DELETE FROM photos WHERE id='$remove' AND userid='$userid'");
}
    
//http://www.reconn.us/content/view/30/51/ Is where the upload came from.

//define a maxim size for the uploaded images in Kb
 define ("MAX_SIZE","100"); 

//This function reads the extension of the file. It is used to determine if the file  is an image by checking the extension.
 function getExtension($str)
 {
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 }

//This variable is used as a flag. The value is initialized with 0 (meaning no error  found)  
//and it will be changed to 1 if an errro occures.  
//If the error occures the file will not be uploaded.
 $errors=0;
//checks if the form has been submitted
 if(isset($_POST['Submit'])) 
 {
 	//reads the name of the file the user submitted for uploading
 	$image=$_FILES['image']['name'];
 	//if it is not empty
 	if ($image) 
 	{
 	//get the original name of the file from the clients machine
 		$filename = stripslashes($_FILES['image']['name']);
 	//get the extension of the file in a lower case format
  		$extension = getExtension($filename);
 		$extension = strtolower($extension);
 	//if it is not a known extension, we will suppose it is an error and will not  upload the file,  
	//otherwise we will do more tests
 if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
 		{
		//print error message
 			echo '<h1>Unknown extension!</h1>';
 			$errors=1;
 		}
 		else
 		{
//get the size of the image in bytes
 //$_FILES['image']['tmp_name'] is the temporary filename of the file
 //in which the uploaded file was stored on the server
 $size=filesize($_FILES['image']['tmp_name']);

//compare the size with the maxim size we defined and print error if bigger
if ($size > MAX_SIZE*1024)
{
	echo '<h1>You have exceeded the size limit!</h1>';
	$errors=1;
}
$imgSize = getimagesize($_FILES['image']['tmp_name']);
                           if ($imgSize[0] > 400 || $imgSize[1] > 400)
                           {
                            print "<h1>You need an image with length and width below 400px! Resize!";
                            $errors=1;
                           }

//we will give an unique name, for example the time in unix time format
$image_name=time().'.'.$extension;
//the new name will be containing the full path where will be stored (images folder)
$newname="images/".$image_name;
//we verify if the image has been uploaded, and print error instead
$copied = copy($_FILES['image']['tmp_name'], $newname);
if (!$copied) 
{
	echo '<h1>Copy unsuccessfull!</h1>';
	$errors=1;
}}}}

//If no errors registred, print the success message
 if(isset($_POST['Submit']) && !$errors) 
 {
 	echo "<h1>File Uploaded Successfully! Upload More!</h1>";
        
        //Insert into photos immediately.
        mysql_query("INSERT INTO photos (userid, photo) VALUES ('$userid', '$newname')");
 }










    //This is coded by Joe Alai
    
    //Get all of the photos already uploaded by the userid
    $q = mysql_query("SELECT * FROM photos");
    print "<table><tr>";
    while ($photos = mysql_fetch_array($q))
    {
        if ($photos['userid'] == $userid)
        {
            $photo = $photos['photo'];
            
            //this is going to shrink the image size
            //Let it be.
            $imageSpecs = scaleImage($photo, 100, 100);
            
            $x = $imageSpecs[0];
            $y = $imageSpecs[1];
            
            //Set the photo id so that we can easily remove them.
            $photoid = $photos['id'];
                        
                        //create a table to make it so they can remove images
            print "<td>
                        <table>
                            <tr>
                                <td>
                                     <img src='$photo' width='$x' height='$y'>
                                 </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href='upload_image.php?remove=$photoid'>Remove</a>
                                </td>
                            </tr>
                        </table>
                        
                        
                  </td>";
        }
    }
    print "</tr></table>";




















 ?>

 <!--next comes the form, you must set the enctype to "multipart/frm-data" and use an input type "file" -->
 <form name="newad" method="post" enctype="multipart/form-data"  action="">
 <table>
 	<tr><td><input type="file" name="image"></td></tr>
 	<tr><td><input name="Submit" type="submit" value="Upload image"></td></tr>
 </table>	
 </form>
 </html>