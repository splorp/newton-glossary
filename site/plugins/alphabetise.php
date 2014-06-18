<?php
// The Alphabetise plugin for Kirby CMS will Alphabetise a given page array or tag array 
// and return it for further processing/display
// The 'key' text field must be longer than a single character.
// See readme for further information
// Recent Updates: Fix for single alphabetical index bug #3 initial bug fix problem
// -----
// Russ Baldwin 
// shoesforindustry.net
// v0.0.7
// -----
function alphabetise($parent, $options=array()) {
  
  // default key values
  $defaults = array('key'=> 'title');
  
  // merge defaults and options
  $options = array_merge($defaults, $options);  

  //Gets the input into a two dimensional array - uses '~' as separator;
  foreach ($parent as $item){
    $temp = explode('~',$item->$options['key']() );
    $temp = $temp[0];
    $temp = strtolower($temp);
    $array[$temp][] = $item;
  }

  // Check to see array is not empty
  if ( (!empty ($array)) )  { 
    //Make an array of key and data
    foreach ($array as $temp => $item){
      if (strlen($temp)<2){
        $temp=$temp.$temp;
        $array[substr($temp, 0, 2)][]=$item[0];
      } else {
        $array[substr($temp, 0, 1)][]=$item[0];
      }
      unset ($array[$temp]); 
    }

    // If all OK $array will be returned and sorted
    ksort($array);
  
  } else {
    
    // There has been a problem so set $array with error message and then return $array
    $array = array(
      "Alphabetise Plugin Error: Problem with array or invalid key!
        Make sure your array is valid, not empty & that the key is valid for this type of array.  (You can probably ignore the errors after this point, until this error has been resolved.)" => "Error"
    );
  }
  
  return $array;
}
?>
