<?php

class Uploader_Image extends Uploader_File
{
  public function setAllowedTypes($types)
  {
    if (!is_array($types)) {
      $types = array($types);
    }

    $this->allowedTypes = array_map('strtolower', $types);
  }

  public function getAllowedTypes()
  {
    return $this->allowedTypes;
  }

  public function uploadImage($data, $fileName = null)
  {
    $ext = self::getType($data);

    if ($ext === false) {
      throw new Exception(__METHOD__ . "() Can't upload. Invalid image type.");
    }

    if (empty($fileName)) {
      $fileName = $this->generateFileName($ext);
    }

    $this->upload($data, $fileName);

    return $fileName;
  }

  public static function getType($data)
  {
    if (strncmp("\xff\xd8", $data, 2) === 0) {
      return 'jpeg';
    } elseif (preg_match('/^GIF8[79]a/', $data) === 1) {
      return 'gif';
    } elseif (strncmp("\x89PNG\x0d\x0a\x1a\x0a", $data, 8) === 0) {
      return 'png';
    } elseif (strncmp("BM", $data, 2) === 0) {
      return 'bitmap';
    } elseif (strncmp("\x00\x00\x01\x00", $data, 4) === 0) {
      return 'ico';
    } elseif (strncmp("\x49\x49\x2a\x00\x08\x00\x00\x00", $data, 8) === 0 ||
              strncmp("\x4d\x4d\x00\x2a\x00\x00\x00\x08", $data, 8) === 0) {
      return 'tiff';
    } else {
      return false;
    }
  }
}
