<?php

//this function for calculate user age by birth of date
function calc_age($dateOfBirth){
    // Create a datetime object using date of birth
    $dobirth = new DateTime($dateOfBirth);

    // Get today's date
    $today = new DateTime();

    // Calculate the time difference between the two dates
    $diff = $today->diff($dobirth);

    // Get the age in years, months and days
    return  $diff->y . " years " . $diff->m . " months " . $diff->d . " days ";
    // print_r($today);
}
//this function for upload image
function uploadImage($folder , $image){
    $image->store('/' , $folder);
    // $fileName = $image->hashName();
    $fileName = $image;
    $path = 'images/'.$folder.'/'.$fileName;
    return $path;
}

