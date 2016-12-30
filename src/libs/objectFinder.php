<?php

return call_user_func(function() {
  $objectFinder = function($object, $arrayFinder = []) use (&$objectFinder) {
    if (empty($arrayFinder)) {
      return $object;
    }
    $find = array_shift($arrayFinder);
    if (is_object($object)) {
      if (empty($arrayFinder)) {
        return $object->$find;
      }
      return $objectFinder($object->$find);
    } else if (is_array($object)) {
      if (empty($arrayFinder)) {
        return $object[$find];
      }
      return $objectFinder($object[$find]);
    }
    return $object;
  };

  return $objectFinder;
});