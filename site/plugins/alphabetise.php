<?php
// The Alphabetise plugin for Kirby CMS will Alphabetise a given page array or tag array 
// and return it for further processing/display
// See readme for further information
// -----
// Russ Baldwin 
// shoesforindustry.net
// v0.0.4
// -----
function alphabetise($parent, $options=array()) {
  
// default key values
  $defaults = array('key'=> 'title');
  
  // merge defaults and options
  $options = array_merge($defaults, $options);  

  //Gets the stuff into a two dimensional array
  foreach ($parent as $item){
          $temp = explode('~',$item->$options['key']() );
          $temp = $temp[0];
          $temp = strtolower($temp);
          $array[$temp][] = $item;
  }

// Check to see array is not empty
if ( (!empty ($array)) )  {  
  //Gets the stuff into a two dimensional array
  foreach ($array as $temp => $item){
          if (count($item) == 1){
                  $array[substr($temp, 0, 1)][]=$item[0];
                  unset ($array[$temp]);
          }
  }

  // All OK $array will be returened
  
  } else {
  // A Problem so set $array with error message and return
  $array = array(
      "Alphabetise Plugin Error: Problem with array or invalid key!
       Make sure your array is valid, not empty & that the key is valid for this type of array.  (You can probably ignore the errors after this point, until this error has been resolved.)" => "Error"
  );
  
}
  return $array;
}
?>
