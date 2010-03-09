<?php

/**
 *
 * INDEXU Deluxe
 * Copyright(C), Nicecoder, 2008, All Rights Reserved.
 *
 */

  function IsDigit($str) {
    if (ereg("^(0|[1-9][0-9]*)$", $str, $regs)) {
      return true;
    }
    else {
      return false;
    }
  }


  function IsMoney($str) {
    if (ereg("^([0-9]+|[0-9]{1,3}(,[0-9]{3})*)(\.[0-9]{1,2})?$", $str, $regs)) {
      return true;
    }
    else {
      return false;
    }
  }


  function IsEmailAddress($str) {
    if (ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$", $str, $regs)) {
      return true;
    }
    else {
      return false;
    }
  }


  function IsAlphanumeric($str) {
    if (ereg("^[a-zA-Z0-9]+$", $str, $regs)) {
      return true;
    }
    else {
      return false;
    }
  }


  function IsAlphanumericUnderscore($str) {
    if (ereg("^[a-zA-Z0-9_]+$", $str, $regs)) {
      return true;
    }
    else {
      return false;
    }
  }
  
  function cleanFieldInput($field) 
  {
    return $field = stripslashes($field);
  }
?>
