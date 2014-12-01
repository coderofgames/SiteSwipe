<!DOCTYPE HTML>
<?php

function grab_image($url,$saveto){
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($saveto)){
        unlink($saveto);
    }
    $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);
}

function multiexplode($delimiters, $string) {

    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return $launch;
}

function grab_element($original_url, $dom, $tagname, $dir_name, $root_dir  )
{
    
    
    echo "<br> entering function". "</br>";
    
    echo "</br>"."tag name: " .$tagname;
    echo "</br>"."dir_name: " .$dir_name;    
    
    foreach($dom->getElementsByTagName($tagname) as $img) {
        $attr = "";
        if( $tagname == "link") $attr = "href";
        if( $tagname == "img") $attr = "src";
        if( $tagname == "script") $attr = "src";
        
        $str=$img->getAttribute($attr);
        
        if( $str == "" )continue;
        
       
        $source = "";
        
        $accum_path = "";

        $saved_url = $str;
        
        // IS THIS AN ABSOLUTE URL OR RELATIVE ?
        if ((substr($str, 0, 7) == 'http://') || (substr($str, 0, 8) == 'https://')) {
        // the current link is an "absolute" URL - parse it to get just the path
            $parsed = parse_url($str);
            $path = $parsed['path'];
            
            $arr = explode("/", $path);
            $arr_size = count($arr);
            $accum_path = $root_dir;
            
            // walk through the path array creating directories
            for( $i = 0; $i < $arr_size-1;$i++)
            {
                $accum_path .= $arr[$i] . "/";
                mkdir($accum_path);
            }
            
            $file_name = $arr[$arr_size-1];
            echo "<br><br> accum_path : ". $accum_path;
            $accum_path .= $file_name;
            echo "<br> saveto : ". $accum_path;
           
            $source = $saved_url;
            
            
            
            
            echo "<br><br> FROM : ". $source;
            

        }
        else {  
            
            $arr = multiexplode("/", $str);
            print_r($arr);
            echo "</br>";
            echo $arr_count = count($arr);
            // walk through the path array creating directories
            $accum_path = $root_dir . "/" . $dir_name . "/";
     
                for( $d=1; $d < $arr_count-1; $d++ )
                {
                    $accum_path .= $arr[$d]."/";
                    mkdir($accum_path);
                }

            $file_name = $arr[$arr_count-1];
            $accum_path .= $file_name;
        
            $source = $original_url . $str;
            
            echo "<br> saveto : ". $accum_path;
   
            echo "<br><br> FROM : ". $source;            
        }
        grab_image($source,$accum_path );
    }
    
}

// assumes a legal url of the form "http://www.foldername.[com,co.uk,be,fr,etc]
function getFolderNameFromUrl($url)
{
    $folder_name = "base";
    $url_explode = multiexplode("/",$url);
        
    //print_r($url_explode);
    $folder_explode = multiexplode(".",$url_explode[2]);

//        print_r($folder_explode);
        
    if( count($folder_explode) >= 3)
    {
        $folder_name = $folder_explode[1];           
    }
    return $folder_name;
}

function pregReplaceAll($find, $replacement, $s) {
        while(preg_match($find, $s)) {
                $s = preg_replace($find, $replacement, $s);
        }
        return $s;
}



/* Main */

if( !empty($_POST['data']))
{
    $url = $_POST['data'];
    $folder_name = "base";
    $url_explode = multiexplode("/",$url);
        
    //print_r($url_explode);
    $folder_explode = multiexplode(".",$url_explode[2]);

//        print_r($folder_explode);
        
    if( count($folder_explode) >= 3)
    {
        $folder_name = $folder_explode[1];           
    }
    echo $root = $url;//$url_explode[0] . "//" . $folder_explode[0] . "." . $folder_explode[1] . "." . $folder_explode[2] . "/" ;
    echo "<br>" . $folder_name;
    
    echo $base = $url_explode[0] . "//" . $folder_explode[0] . "." . $folder_explode[1] . "." . $folder_explode[2] . "/" ;

    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $html = curl_exec($ch);
    curl_close($ch);
    
    
    $copy_html = $html;
    $froot_name = "index.html";




    // make some folders
    mkdir($folder_name . "/");
    mkdir($folder_name . "/css/");
    mkdir($folder_name . "/images/");
    mkdir($folder_name . "/js/");
    
   // $copy_html = pregReplaceAll( , "", $copy_html );
       //     while(preg_match("/$root/", $copy_html)) {
       //         $copy_html = preg_replace($root, '', $copy_html);
      //  }
        
    $copy_html = str_replace($base, '',$copy_html);
    // NOTE: replace all with web page pattern "http://www.somesite.com, etc"
   
    $file = fopen($folder_name . "/" .$froot_name, 'w');
    fwrite($file, $copy_html);
    fclose($file);

    // Create a DOM parser object
    $dom = new DOMDocument();
    
    // The @ before the method call suppresses any warnings
    @$dom->loadHTML($html);
    
    echo "</br>"."root: " .$root;
    echo "</br>"."folder name: " .$folder_name;

    grab_element($root, $dom, 'link', 'css', $folder_name);
    grab_element($root, $dom, 'script', 'js', $folder_name);
    grab_element($root, $dom, 'img', 'images', $folder_name);


}
?>

<head>
    
</head>
<body>
<h3>Contact</h3>
							<form method="post" action="#">
								<div class="row 50%">
									<div class="6u 12u(3)">
										<input name="data" placeholder="Name" type="text" />
									</div>

								</div>
								<div class="row 50%">
									<div class="12u">
										<ul class="actions">
											<li><input type="submit" value="Get Site" /></li>
											<li><input type="reset" value="Clear form" /></li>
										</ul>
									</div>
								</div>
							</form>
                                                        </body>