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
        echo "<br>Getting attribute: ".$attr.", from tag: ".$tagname." ;";
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
            $parsed = parse_url($original_url);
            $path = $parsed['path'];
            $host = $parsed['host'];
            $scheme = $parsed['scheme'];
            
            $explode_host = explode(".", $host);
            $my_root = "";
            if( count($explode_host) >= 3){
                    $my_root = $explode_host[1];// ."/". $the_path;
                }
                else
                {
                    $my_root = $explode_host[0];// ."/". $the_path;
                }
                
                mkdir($my_root);
            
            $arr = explode("/", $str);
            print_r($arr);
            echo "</br>";
            echo $arr_count = count($arr);
            $the_path ="";
            // walk through the path array creating directories
            $accum_path = $my_root ."/";
     
            $first_up_count = 0;
            for( $d=0; $d < $arr_count-1; $d++ )
            {
                if( $arr[$d] == '..' )
                {
                    $first_up_count++;
                    continue;
                }
                $accum_path .= $arr[$d]."/";
                $the_path .= $arr[$d] ."/";
                

                mkdir($accum_path);
                mkdir($the_path);
            }

            $file_name = $arr[$arr_count-1];
            
            
            
            $accum_path .= $file_name;
        
            $url_for_root = "";
            
            // need to explode the string "$str" to go up a directory
            $url_for_root .= $scheme . "://" . $host ."/". $the_path;
            
            echo " URL FOR ROOT: ( ". $url_for_root . " )";
            
            
            $source = $url_for_root . $file_name ;
            
            if( $first_up_count != 0)
            {
                
                
                
                    $accum_path = $my_root ."/". $the_path .$file_name;
                
                
            }
                
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
    $url_explode = explode("/",$url);
    print_r($url_explode);
    
    // we now need to count the elements *past* [2]
    
    $parsed = parse_url($url);
            $path = $parsed['path'];
            $host = $parsed['host'];
            $scheme = $parsed['scheme'];
    
    $path_array = explode("/",$path);
    print_r($path_array);
    $new_path = "";
    $path_array_count = count($path_array);
    if( strpos($path_array[$path_array_count-1],".html") || strpos($path_array[$path_array_count-1],".htm") || strpos($path_array[$path_array_count-1],".php")  )
    {
        $new_path = "";
        
        for( $i=0; $i<$path_array_count-1;$i++)
        {
            $new_path .= $path_array[$i] . "/";
        }
    }
    else
    {
        $new_path = "";
        
        for( $i=0; $i<$path_array_count;$i++)
        {
            $new_path .= $path_array[$i] . "/";
        }        
    }
    echo "<br> NEW PATH : ".$new_path;
    
    $base = "";    
    //print_r($url_explode);
    
    
    
    
    
    
    
    $folder_explode = explode(".",$url_explode[2]);


        
    $folder_count=count($folder_explode);
    
    if( $folder_count >= 3)
    {
        $folder_name = $folder_explode[1];
        //echo "<br>";
        //echo $base = $url_explode[0] . "//" . $folder_explode[0] . "." . $folder_explode[1] . "." . $folder_explode[2] . "/" ;
    }
    else if( $folder_count == 2 )
    {
        $folder_name = $folder_explode[0];
        //echo "<br>";
        //echo $base = $url_explode[0] . "//" . $folder_explode[0] . "." . $folder_explode[1] . "/" ;
    }
    else {
        echo "<br>strange website name: die";
        die;
    }
    
    echo $root = $scheme . "://". $host . $new_path;//$url_explode[0] . "//" . $folder_explode[0] . "." . $folder_explode[1] . "." . $folder_explode[2] . "/" ;
    echo "<br>" . $folder_name;
    
    
    

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
    mkdir($folder_name . $new_path );
    $accum_path="";
    for( $i=0; $i<$path_array_count-1;$i++)
        {
            $accum_path .= $path_array[$i] . "/";
            mkdir($folder_name . "/" . $accum_path);
        }
    echo "<br> folder_name: ".$folder_name . $new_path;
    mkdir($folder_name . "/css/");
    mkdir($folder_name . "/images/");
    mkdir($folder_name . "/js/");
    
   // $copy_html = pregReplaceAll( , "", $copy_html );
       //     while(preg_match("/$root/", $copy_html)) {
       //         $copy_html = preg_replace($root, '', $copy_html);
      //  }
        
    $copy_html = str_replace($scheme. "://".$host . "/", '',$copy_html);
    // NOTE: replace all with web page pattern "http://www.somesite.com, etc"
   
    $file = fopen($folder_name  . $new_path . "/". $froot_name, 'w');
    fwrite($file, $copy_html);
    fclose($file);

    // Create a DOM parser object
    $dom = new DOMDocument();
    
    // The @ before the method call suppresses any warnings
    @$dom->loadHTML($html);
    
    /*
    // check that we don't have to look up a directory anywhere
    foreach($dom->getElementsByTagName('link') as $lnk) {
        $str=$lnk->getAttribute('href');
        if( substr($str,0,3) == '../')
        {
            // we are going up a directory, so count how far up we are going thenresave the html
        }
    }
    foreach($dom->getElementsByTagName('img') as $lmg) {
        $str=$img->getAttribute('src');
        if( substr($str,0,3) == '../')
        {
            // we are going up a directory, so count how far up we are going thenresave the html
        }
    }
    foreach($dom->getElementsByTagName('script') as $scrpt) {
        $str=$scrpt->getAttribute('src');
        if( substr($str,0,3) == '../')
        {
            // we are going up a directory, so count how far up we are going thenresave the html
        }
    }
    */
    
    $folder_name  .= $new_path ;
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