<?php
// The Alphabetise plugin for Kirby CMS will Alphabetise a given page array or tag array
// and return it for further processing/display
// The 'key' text field must be longer than a single character.
// See readme for further information
// Recent Updates: Added key for ksort sort_flags - set orderby to SORT_STRING for numbers & then letters
// Default it is set to SORT_REGLAUR for letters and then numbers
// 'orderby' key is NOT a string!
// -----
// Russ Baldwin
// shoesforindustry.net
// v0.0.9
// -----
function alphabetise($parent, $options=array()) {

  // default key values
  // As we are using ksort the default 'orderby' is SORT_REGULAR
  // To sort with number first you can use 'orderby' set to SORT_STRING
  // Other ksort sort_flags may be usuable but not tested!
  $defaults = array('key'=> 'title','orderby'=> SORT_REGULAR);

  // merge defaults and options
  $options = array_merge($defaults, $options);

  //Gets the input into a two dimensional array - uses '~' as separator;
  foreach ($parent as $item){
    $temp = explode('~',$item->{$options['key']}() );
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
  ksort($array,$options['orderby']);

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
